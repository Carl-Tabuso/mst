<?php

namespace App\Services;

use App\Filters\ApplyDateOfArchivalRange;
use App\Filters\Employee\FilterCreatedBetween;
use App\Filters\Employee\FilterPositions;
use App\Filters\Employee\FilterStatuses;
use App\Filters\Employee\SearchDetails;
use App\Filters\FilterOnlyArchived;
use App\Mail\NewUserCredentials;
use App\Models\Employee;
use App\Models\EmployeeCompensation;
use App\Models\EmployeeEmergencyContact;
use App\Models\EmployeeEmploymentDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Str;

class EmployeeService
{
    public function getAllEmployees(?int $perPage = 10, ?string $search = '', ?array $filters = []): mixed
    {
        $pipes = [
            new SearchDetails($search),
            new FilterStatuses($filters['accountStatuses'] ?? []),
            new FilterCreatedBetween($filters['fromDateCreated'] ?? null, $filters['toDateCreated'] ?? null),
            new FilterPositions($filters),
        ];

        return Pipeline::send(Employee::query())
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage) {
                return $query->with(['emergencyContact', 'employmentDetails', 'compensation', 'account', 'position'])
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->paginate($perPage)
                    ->withQueryString();
            });
    }

    public function getArchivedEmployees(int $perPage = 10, ?string $search = '', ?array $filters = [])
    {
        $archivedAtColumn = new Employee()->getDeletedAtColumn();

        $pipes = [
            new FilterOnlyArchived,
            new SearchDetails($search),
            new ApplyDateOfArchivalRange($archivedAtColumn, $filters),
            new FilterPositions($filters),
        ];

        return Pipeline::send(Employee::with('position'))
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage, $archivedAtColumn) {
                return $query
                    ->latest($archivedAtColumn)
                    ->paginate($perPage)
                    ->withQueryString()
                    ->toResourceCollection();
            });
    }

    public function getEmployeesForRatingsExport(?array $filters = [])
    {
        $allowedPositions = ['Driver', 'Hauler', 'Team Leader', 'Frontliner', 'Safety Officer'];

        $query = Employee::with([
            'position',
            'performancesAsEmployee.jobOrder',
            'performancesAsEmployee.ratings.performanceRating',
            'evaluatedTeamLeaders.jobOrder',
            'evaluatedTeamLeaders.ratings.performanceRating',
            'createdJobOrders',
        ])->whereHas('position', fn($q) => $q->whereIn('name', $allowedPositions));

        if (!empty($filters['positions'])) {
            $validPositions = array_intersect($filters['positions'], $allowedPositions);
            if ($validPositions) {
                $query->whereHas('position', fn($q) => $q->whereIn('name', $validPositions));
            }
        }

        $employees = $query->get();

        return $employees->map(function ($employee) {
            $position = strtolower($employee->position->name ?? '');

            $performanceStats = $this->getPerformanceStatistics($employee, $position);

            $jobOrderStats = $this->getEmployeeJobOrderStats($employee);

            $averageRating = $this->calculateAverageRating($employee);
            $completionRate = $jobOrderStats['total_job_orders'] > 0
                ? round(($jobOrderStats['completed_job_orders'] / $jobOrderStats['total_job_orders']) * 100, 2)
                : 0;

            return (object) array_merge([
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'position' => $employee->position->name ?? 'Unknown',
                'average_rating' => $averageRating,
                'total_ratings' => $this->getTotalRatings($employee),
                'has_ratings' => $this->getTotalRatings($employee) > 0,
                'total_job_orders' => $jobOrderStats['total_job_orders'],
                'completed_job_orders' => $jobOrderStats['completed_job_orders'],
                'dropped_job_orders' => $jobOrderStats['dropped_job_orders'],
                'completion_rate' => $completionRate,
            ], $performanceStats);
        });
    }

    private function getPerformanceStatistics($employee, $position)
    {
        $stats = [
            'job_orders_created' => 0,
            'completed_job_orders_created' => 0,
            'corrections_made' => 0,
            'total_errors' => 0,
            'success_rate_created' => 0,
        ];

        // Frontliner-specific statistics (job orders they created)
        if ($position === 'frontliner' && $employee->createdJobOrders) {
            $createdJobOrders = $employee->createdJobOrders;

            $stats['job_orders_created'] = $createdJobOrders->count();
            $stats['completed_job_orders_created'] = $createdJobOrders->where('status', 'completed')->count();

            // Count job orders with corrections
            $stats['corrections_made'] = $createdJobOrders->filter(function ($jobOrder) {
                return $jobOrder->corrections && $jobOrder->corrections->count() > 0;
            })->count();

            // Sum error counts (assuming there's an error_count field)
            $stats['total_errors'] = $createdJobOrders->sum('error_count') ?? 0;

            // Calculate success rate for created job orders
            $stats['success_rate_created'] = $stats['job_orders_created'] > 0
                ? round(($stats['completed_job_orders_created'] / $stats['job_orders_created']) * 100, 2)
                : 0;
        }

        return $stats;
    }

    private function getEmployeeJobOrderStats($employee)
    {
        $total = 0;
        $completed = 0;
        $dropped = 0;

        // Count from performancesAsEmployee (when employee is being evaluated)
        if ($employee->performancesAsEmployee) {
            foreach ($employee->performancesAsEmployee as $performance) {
                if ($performance->jobOrder) {
                    $total++;
                    if ($performance->jobOrder->status === 'completed') $completed++;
                    if (in_array($performance->jobOrder->status, ['cancelled', 'dropped'])) $dropped++;
                }
            }
        }

        // Count from evaluatedTeamLeaders (when employee is evaluating others as team leader)
        if ($employee->evaluatedTeamLeaders) {
            foreach ($employee->evaluatedTeamLeaders as $performance) {
                if ($performance->jobOrder) {
                    $total++;
                    if ($performance->jobOrder->status === 'completed') $completed++;
                    if (in_array($performance->jobOrder->status, ['cancelled', 'dropped'])) $dropped++;
                }
            }
        }

        return [
            'total_job_orders' => $total,
            'completed_job_orders' => $completed,
            'dropped_job_orders' => $dropped,
        ];
    }

    private function calculateAverageRating($employee)
    {
        $ratings = collect();

        // Get ratings from performancesAsEmployee (ratings received as employee)
        if ($employee->performancesAsEmployee) {
            foreach ($employee->performancesAsEmployee as $performance) {
                if ($performance->ratings) {
                    $ratings = $ratings->merge($performance->ratings->pluck('performanceRating.scale'));
                }
            }
        }

        // Get ratings from evaluatedTeamLeaders (ratings given as team leader)
        if ($employee->evaluatedTeamLeaders) {
            foreach ($employee->evaluatedTeamLeaders as $performance) {
                if ($performance->ratings) {
                    $ratings = $ratings->merge($performance->ratings->pluck('performanceRating.scale'));
                }
            }
        }

        $filteredRatings = $ratings->filter();
        return $filteredRatings->count() > 0 ? round($filteredRatings->avg(), 2) : null;
    }

    private function getTotalRatings($employee)
    {
        $total = 0;

        // Count ratings from performancesAsEmployee
        if ($employee->performancesAsEmployee) {
            foreach ($employee->performancesAsEmployee as $performance) {
                $total += $performance->ratings->count();
            }
        }

        // Count ratings from evaluatedTeamLeaders
        if ($employee->evaluatedTeamLeaders) {
            foreach ($employee->evaluatedTeamLeaders as $performance) {
                $total += $performance->ratings->count();
            }
        }

        return $total;
    }

    public function createEmployee(array $validated)
    {
        return DB::transaction(function () use ($validated) {
            $employee = Employee::create([
                'last_name'        => $validated['last_name'],
                'first_name'       => $validated['first_name'],
                'middle_name'      => $validated['middle_name']    ?? null,
                'suffix'           => $validated['suffix']         ?? null,
                'date_of_birth'    => $validated['date_of_birth']  ?? null,
                'email'            => $validated['email']          ?? null,
                'contact_number'   => $validated['contact_number'] ?? null,
                'position_id'      => $validated['position_id'],
                'region'           => $validated['region']           ?? null,
                'province'         => $validated['province']         ?? null,
                'city'             => $validated['city']             ?? null,
                'zip_code'         => $validated['zip_code']         ?? null,
                'detailed_address' => $validated['detailed_address'] ?? null,
            ]);

            $this->createEmergencyContact($employee, $validated);
            $this->createEmploymentDetails($employee, $validated);
            $this->createCompensation($employee, $validated);
            $this->createUserAccount($employee, $validated);

            return $employee;
        });
    }

    public function updateEmployee(Employee $employee, array $validated)
    {
        return DB::transaction(function () use ($employee, $validated) {
            $employee->update([
                'last_name'        => $validated['last_name'],
                'first_name'       => $validated['first_name'],
                'middle_name'      => $validated['middle_name']    ?? null,
                'suffix'           => $validated['suffix']         ?? null,
                'date_of_birth'    => $validated['date_of_birth']  ?? null,
                'email'            => $validated['email']          ?? null,
                'contact_number'   => $validated['contact_number'] ?? null,
                'position_id'      => $validated['position_id'],
                'region'           => $validated['region']           ?? null,
                'province'         => $validated['province']         ?? null,
                'city'             => $validated['city']             ?? null,
                'zip_code'         => $validated['zip_code']         ?? null,
                'detailed_address' => $validated['detailed_address'] ?? null,
            ]);

            $this->updateOrCreateEmergencyContact($employee, $validated);
            $this->updateOrCreateEmploymentDetails($employee, $validated);
            $this->updateOrCreateCompensation($employee, $validated);

            return $employee;
        });
    }

    protected function createEmergencyContact(Employee $employee, array $validated)
    {
        if (! empty($validated['emergency_first_name'])) {
            EmployeeEmergencyContact::create([
                'employee_id'    => $employee->id,
                'last_name'      => $validated['emergency_last_name'] ?? null,
                'first_name'     => $validated['emergency_first_name'],
                'middle_name'    => $validated['emergency_middle_name']    ?? null,
                'suffix'         => $validated['emergency_suffix']         ?? null,
                'contact_number' => $validated['emergency_contact_number'] ?? null,
                'relation'       => $validated['emergency_relation']       ?? null,
            ]);
        }
    }

    protected function updateOrCreateEmergencyContact(Employee $employee, array $validated)
    {
        if ($employee->emergencyContact) {
            $employee->emergencyContact->update([
                'last_name'      => $validated['emergency_last_name']      ?? null,
                'first_name'     => $validated['emergency_first_name']     ?? null,
                'middle_name'    => $validated['emergency_middle_name']    ?? null,
                'suffix'         => $validated['emergency_suffix']         ?? null,
                'contact_number' => $validated['emergency_contact_number'] ?? null,
                'relation'       => $validated['emergency_relation']       ?? null,
            ]);
        } elseif (! empty($validated['emergency_first_name'])) {
            $this->createEmergencyContact($employee, $validated);
        }
    }

    protected function createEmploymentDetails(Employee $employee, array $validated)
    {
        if (! empty($validated['sss_number']) || ! empty($validated['date_hired'])) {
            EmployeeEmploymentDetail::create([
                'employee_id'         => $employee->id,
                'sss_number'          => $validated['sss_number']          ?? null,
                'philhealth_number'   => $validated['philhealth_number']   ?? null,
                'pagibig_number'      => $validated['pagibig_number']      ?? null,
                'tin'                 => $validated['tin']                 ?? null,
                'date_hired'          => $validated['date_hired']          ?? null,
                'regularization_date' => $validated['regularization_date'] ?? null,
                'end_of_contract'     => $validated['end_of_contract']     ?? null,
            ]);
        }
    }

    protected function updateOrCreateEmploymentDetails(Employee $employee, array $validated)
    {
        if ($employee->employmentDetails) {
            $employee->employmentDetails->update([
                'sss_number'          => $validated['sss_number']          ?? null,
                'philhealth_number'   => $validated['philhealth_number']   ?? null,
                'pagibig_number'      => $validated['pagibig_number']      ?? null,
                'tin'                 => $validated['tin']                 ?? null,
                'date_hired'          => $validated['date_hired']          ?? null,
                'regularization_date' => $validated['regularization_date'] ?? null,
                'end_of_contract'     => $validated['end_of_contract']     ?? null,
            ]);
        } elseif (! empty($validated['sss_number']) || ! empty($validated['date_hired'])) {
            $this->createEmploymentDetails($employee, $validated);
        }
    }

    protected function createCompensation(Employee $employee, array $validated)
    {
        if (! empty($validated['salary']) || ! empty($validated['allowance'])) {
            EmployeeCompensation::create([
                'employee_id' => $employee->id,
                'salary'      => $validated['salary']    ?? null,
                'allowance'   => $validated['allowance'] ?? null,
            ]);
        }
    }

    protected function updateOrCreateCompensation(Employee $employee, array $validated)
    {
        if ($employee->compensation) {
            $employee->compensation->update([
                'salary'    => $validated['salary']    ?? null,
                'allowance' => $validated['allowance'] ?? null,
            ]);
        } elseif (! empty($validated['salary']) || ! empty($validated['allowance'])) {
            $this->createCompensation($employee, $validated);
        }
    }

    protected function createUserAccount(Employee $employee, array $validated)
    {
        if (! empty($validated['create_account']) && ! empty($validated['account_email'])) {
            $password = Str::random(12);

            $user = User::create([
                'employee_id' => $employee->id,
                'email'       => $validated['account_email'],
                'password'    => Hash::make($password),
            ]);

            Mail::to($user->email)->send(new NewUserCredentials($user, $password));
        }
    }

    public function restoreArchivedEmployee(Employee $employee): bool
    {
        return $employee->restore();
    }

    public function bulkRestoreArchivedEmployees(array $employeeIds): mixed
    {
        return Employee::query()
            ->onlyTrashed()
            ->whereIn('id', $employeeIds)
            ->restore();
    }

    public function bulkArchiveEmployees(array $employeeIds): mixed
    {
        return Employee::query()
            ->whereIn('id', $employeeIds)
            ->delete();
    }
    public function permanentlyDeleteEmployee(Employee $employee): ?bool
    {
        return $employee->forceDelete();
    }
}
