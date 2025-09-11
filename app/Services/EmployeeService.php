<?php

namespace App\Services;

use App\Filters\Employee\FilterCreatedBetween;
use App\Filters\Employee\FilterStatuses;
use App\Filters\Employee\SearchDetails;
use App\Mail\NewUserCredentials;
use App\Models\Employee;
use App\Models\EmployeeCompensation;
use App\Models\EmployeeEmergencyContact;
use App\Models\EmployeeEmploymentDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmployeeService
{
    public function getAllEmployees(?int $perPage = 10, ?string $search = '', ?array $filters = []): mixed
    {
        $pipes = [
            new SearchDetails($search),
            new FilterStatuses($filters['accountStatuses'] ?? []),
            new FilterCreatedBetween($filters['fromDateCreated'] ?? null, $filters['toDateCreated'] ?? null),
        ];

        return app(Pipeline::class)
            ->send(Employee::query())
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage) {
                return $query->with(['emergencyContact', 'employmentDetails', 'compensation', 'account', 'position'])
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->paginate($perPage)
                    ->withQueryString();
            });
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

    public function getEmployeesForDropdown()
    {
        return Employee::select('id', 'first_name', 'last_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn ($employee) => [
                'id'   => $employee->id,
                'name' => "{$employee->first_name} {$employee->last_name}",
            ]);
    }
}
