<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Exports\EmployeeHistoryRatingsExport;
use App\Exports\EmployeeRatingsExport;
use App\Models\Employee;
use App\Models\EmployeePerformance;
use App\Models\EmployeeRating;
use App\Models\Form3;
use App\Models\JobOrder;
use App\Models\PerformanceSummary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeRatingController extends Controller
{
    private const ALLOWED_POSITIONS = ['Driver', 'Hauler', 'Team Leader', 'Safety Officer', 'Mechanic'];

    private const CONSULTANT_POSITION = 'Consultant';

    private const OVERALL_CATEGORY_ID = 1;

    // ==================== MAIN METHODS ====================

    public function index(Request $request)
    {
        $employee = $this->getAuthenticatedEmployee();

        $filters              = $this->parseIndexFilters($request);
        $allForm3Ids          = $this->getAuthorizedForm3Ids($employee);
        $alreadyRatedForm3Ids = $this->getAlreadyRatedForm3Ids($employee);

        $jobOrders          = $this->getFilteredJobOrders($filters, $allForm3Ids, $alreadyRatedForm3Ids);
        $paginatedJobOrders = $this->paginateJobOrders($jobOrders, $request);

        return Inertia::render('ratings/pages/Index', [
            'jobOrders'  => $paginatedJobOrders,
            'employeeId' => $employee->id,
            'filters'    => $filters,
        ]);
    }

    public function create(Request $request)
    {
        $employee = $this->getAuthenticatedEmployee();

        $jobOrderTicket = $request->input('job_order_ticket');
        if (! $jobOrderTicket) {
            return redirect()->route('employee.ratings.index')
                ->with('error', 'Job order ticket is required.');
        }

        $jobOrderId = $this->extractIdFromTicket($jobOrderTicket);
        if (! $jobOrderId) {
            return redirect()->route('employee.ratings.index')
                ->with('error', 'Invalid job order ticket format.');
        }

        $allForm3Ids          = $this->getAuthorizedForm3Ids($employee);
        $alreadyRatedForm3Ids = $this->getAlreadyRatedForm3Ids($employee);

        $selectedJobOrder = $this->getValidJobOrderForRating($jobOrderId, $allForm3Ids, $alreadyRatedForm3Ids);
        if (! $selectedJobOrder) {
            return redirect()->route('employee.ratings.index')
                ->with('error', 'Invalid or unauthorized job order.');
        }

        $teamMembersToRate = $this->getTeamMembersToRate($selectedJobOrder, $employee);

        return Inertia::render('ratings/pages/EmployeeRating', [
            'jobOrders'         => [$this->formatJobOrderForFrontend($selectedJobOrder)],
            'employeeId'        => $employee->id,
            'teamMembersToRate' => $teamMembersToRate,
        ]);
    }

    public function view(Request $request)
    {
        $employee = $this->getAuthenticatedEmployee();

        $jobOrderTicket = $request->input('job_order_ticket');
        if (! $jobOrderTicket) {
            return redirect()->route('employee.ratings.index')
                ->with('error', 'Job order ticket is required.');
        }

        $jobOrderId = $this->extractIdFromTicket($jobOrderTicket);
        if (! $jobOrderId) {
            return redirect()->route('employee.ratings.index')
                ->with('error', 'Invalid job order ticket format.');
        }

        $allForm3Ids = $this->getAuthorizedForm3Ids($employee);
        $targetOrder = $this->getJobOrderWithRatings($jobOrderId, $employee, $allForm3Ids);

        if (! $targetOrder) {
            return redirect()->route('employee.ratings.index')
                ->with('error', 'Invalid or unauthorized job order.');
        }

        $ratedTeamMembers = $this->getRatedTeamMembers($targetOrder);

        return Inertia::render('ratings/pages/ViewEmployeeRating', [
            'jobOrders'        => [$this->formatJobOrderForFrontend($targetOrder)],
            'employeeId'       => $employee->id,
            'ratedTeamMembers' => $ratedTeamMembers,
        ]);
    }

    public function store(Request $request)
    {
        $employee = $this->getAuthenticatedEmployee();
        $data     = $this->validateRatingData($request);

        $jobOrderId = $data['ratings'][0]['job_order_id'];

        $summary = PerformanceSummary::updateOrCreate(
            ['job_order_id' => $jobOrderId],
            ['overall_remarks' => $data['overall_remarks'] ?? null]
        );

        $this->processRatings($data['ratings'], $employee, $summary);

        return back()->with('success', 'Performance ratings submitted successfully!');

    }

    public function ratingsTable(Request $request)
    {
        $filters           = $this->parseTableFilters($request);
        $filteredEmployees = $this->getFilteredEmployees($filters);

        if ($search = $request->input('search')) {
            $filteredEmployees = $this->applySearch($filteredEmployees, $search);
        }

        $paginatedEmployees = $this->paginateEmployees($filteredEmployees, $request);

        return inertia('ratings/pages/RatingTable', [
            'employees'        => $paginatedEmployees,
            'allowedPositions' => self::ALLOWED_POSITIONS,
            'filters'          => array_merge($filters, ['search' => $search]),
        ]);
    }

    public function historyPage(Request $request, $employeeId)
    {
        $employee = Employee::with([
            'performancesAsEmployee.jobOrder',
            'performancesAsEmployee.ratings.performanceRating',
            'performancesAsEmployee.evaluator.position',
            'position',
        ])->findOrFail($employeeId);

        $filters = $this->parseHistoryFilters($request);
        $search  = $request->input('search');

        $ratings = $this->getEmployeeRatings($employee, $filters);

        if ($search) {
            $ratings = $this->applyHistorySearch($ratings, $search);
        }

        $sortedRatings    = $this->sortRatings($ratings, $filters['sort']);
        $paginatedRatings = $this->paginateRatings($sortedRatings, $request);

        return Inertia::render('ratings/pages/RatingHistory', [
            'employee' => [
                'id'        => $employee->id,
                'full_name' => $employee->full_name,
                'position'  => $employee->position->name ?? '',
            ],
            'received' => $paginatedRatings,
            'filters'  => array_merge($filters, ['search' => $search]),
        ]);
    }

    public function export(Request $request)
    {

        try {
            $filters = $this->parseTableFilters($request);

            $filteredEmployees = $this->getFilteredEmployees($filters);

            if ($search = $request->input('search')) {
                $filteredEmployees = $this->applySearch($filteredEmployees, $search);
            }

            $filename = $this->generateExportFilename($filters);

            return Excel::download(
                new EmployeeRatingsExport($filteredEmployees, $filters),
                $filename,
                \Maatwebsite\Excel\Excel::XLSX,
                [
                    'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]
            );
        } catch (\Exception $e) {
            Log::error('Export failed', [
                'error'   => $e->getMessage(),
                'user_id' => auth()->id(),
                'filters' => $filters ?? [],
            ]);

            return response()->json(['error' => 'Export failed. Please try again.'], 500);
        }
    }

    public function exportHistory(Request $request, $employeeId)
    {

        $employee = Employee::with([
            'performancesAsEmployee.jobOrder',
            'performancesAsEmployee.ratings.performanceRating',
            'performancesAsEmployee.evaluator.position',
            'position',
        ])->findOrFail($employeeId);

        try {
            $filters = $this->parseHistoryFilters($request);
            $ratings = $this->getEmployeeRatings($employee, $filters);

            if ($search = $request->input('search')) {
                $ratings = $this->applyHistorySearch($ratings, $search);
            }

            $sortedRatings = $this->sortRatings($ratings, $filters['sort']);

            $filename = $this->generateHistoryExportFilename($employee, $filters);

            return Excel::download(
                new EmployeeHistoryRatingsExport($employee, $sortedRatings, $filters),
                $filename,
                \Maatwebsite\Excel\Excel::XLSX,
                [
                    'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]
            );
        } catch (\Exception $e) {
            Log::error('History export failed', [
                'error'       => $e->getMessage(),
                'user_id'     => auth()->id(),
                'employee_id' => $employeeId,
                'filters'     => $filters ?? [],
            ]);

            return response()->json(['error' => 'Export failed. Please try again.'], 500);
        }
    }

    // ==================== TICKET HANDLING METHODS ====================

    private function extractIdFromTicket(string $ticket): ?int
    {
        $currentYear = now()->format('y');
        $prefix      = "JO-{$currentYear}";

        if (! str_starts_with($ticket, $prefix)) {
            return null;
        }

        $numericPart = str_replace($prefix, '', $ticket);
        $id          = (int) $numericPart;

        return $id > 0 ? $id : null;
    }

    private function formatJobOrderForFrontend($jobOrder): array
    {
        $formatted           = $jobOrder->toArray();
        $formatted['ticket'] = $jobOrder->ticket;

        return $formatted;
    }

    private function formatJobOrdersForFrontend($jobOrders)
    {
        return $jobOrders->map(function ($jobOrder) {
            return $this->formatJobOrderForFrontend($jobOrder);
        });
    }

    // ==================== AUTHORIZATION METHODS ====================

    private function getAuthenticatedEmployee()
    {
        $user     = auth()->user();
        $employee = $user->employee()->with('position')->first();

        if (! $employee || ! $employee->position) {
            abort(403, 'Unauthorized - No employee record found');
        }

        return $employee;
    }

    private function authorizeConsultant($employee)
    {
        if ($employee->position->name !== self::CONSULTANT_POSITION) {
            abort(403, 'Unauthorized - Consultant access required');
        }
    }

    // ==================== FORM3 & JOB ORDER METHODS ====================

    private function getAuthorizedForm3Ids($employee)
    {

        $personnelForm3Ids = Form3::query()
            ->whereHas('haulings.assignedPersonnel', function ($q) use ($employee) {
                $q->where('team_leader', $employee->id);

            })
            ->pluck('id')
            ->toArray();

        $haulerForm3Ids = DB::table('form3_haulers')
            ->join('form3_haulings', 'form3_haulers.form3_hauling_id', '=', 'form3_haulings.id')
            ->where('form3_haulers.hauler', $employee->id)
            ->distinct()
            ->pluck('form3_haulings.form3_id')
            ->toArray();

        $driverForm3Ids = DB::table('form3_drivers')
            ->join('form3_haulings', 'form3_drivers.form3_hauling_id', '=', 'form3_haulings.id')
            ->where('form3_drivers.driver', $employee->id)
            ->distinct()
            ->pluck('form3_haulings.form3_id')
            ->toArray();

        return array_unique(array_merge($haulerForm3Ids, $driverForm3Ids, $personnelForm3Ids));

    }

    private function getAlreadyRatedForm3Ids($employee)
    {
        static $cachedRatedForm3Ids = [];

        $employeeId = $employee->id;
        if (isset($cachedRatedForm3Ids[$employeeId])) {
            return $cachedRatedForm3Ids[$employeeId];
        }

        $ratedForm3Ids = DB::table('employee_performances as ep')
            ->join('job_orders as jo', 'ep.job_order_id', '=', 'jo.id')
            ->join('form4 as f4', 'jo.serviceable_id', '=', 'f4.id')
            ->join('form3 as f3', 'f4.id', '=', 'f3.form4_id')
            ->where('ep.evaluator_id', $employeeId)
            ->where('jo.serviceable_type', 'form4')
            ->whereNull('ep.deleted_at')
            ->distinct()
            ->pluck('f3.id')
            ->toArray();

        $cachedRatedForm3Ids[$employeeId] = $ratedForm3Ids;

        return $ratedForm3Ids;
    }

    private function getFilteredJobOrders($filters, $allForm3Ids, $alreadyRatedForm3Ids)
    {
        $query = JobOrder::where('status', JobOrderStatus::Completed)
            ->where('serviceable_type', 'form4')
            ->with([
                'serviceable',
                'serviceable.form3',
                'serviceable.form3.haulings.haulers.position',
                'serviceable.form3.haulings.drivers.position',

                'serviceable.form3.haulings.assignedPersonnel.teamLeader.position',

            ])
            ->latest();

        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $search = $filters['search'];

                if (preg_match('/^JO-\d{2}[\d-]+$/', $search)) {
                    $extractedId = $this->extractIdFromTicket($search);
                    if ($extractedId) {
                        $q->where('id', $extractedId);
                    }
                } else {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhereDate('created_at', $search);
                }
            });
        }

        if ($filters['fromDateOfService']) {
            $query->whereDate('created_at', '>=', $filters['fromDateOfService']);
        }

        if ($filters['toDateOfService']) {
            $query->whereDate('created_at', '<=', $filters['toDateOfService']);
        }

        $completedJobOrders = $query->get();

        $filtered = $completedJobOrders->filter(function ($jobOrder) use ($allForm3Ids) {
            $form3Id = optional(optional($jobOrder->serviceable)->form3)->id;

            return $form3Id && in_array($form3Id, $allForm3Ids);
        })->values();

        $filtered = $filtered->map(function ($jobOrder) use ($alreadyRatedForm3Ids) {
            $form3Id                 = optional(optional($jobOrder->serviceable)->form3)->id;
            $jobOrder->rating_status = in_array($form3Id, $alreadyRatedForm3Ids)
                ? 'Evaluation Done'
                : 'To be Evaluated';
            $jobOrder->ticket = $jobOrder->ticket;

            return $jobOrder;
        });

        if (! empty($filters['statuses'])) {
            $filtered = $filtered->filter(function ($jobOrder) use ($filters) {
                return in_array($jobOrder->rating_status, $filters['statuses']);
            })->values();
        }

        if ($filters['search']) {
            $filtered = $filtered->filter(function ($jobOrder) use ($filters) {
                $search = strtolower($filters['search']);

                return str_contains(strtolower($jobOrder->rating_status), $search)
                || str_contains((string) $jobOrder->id, $search)
                || str_contains(strtolower($jobOrder->ticket), $search)
                || str_contains($jobOrder->created_at->format('Y-m-d H:i:s'), $search);
            })->values();
        }

        return $this->formatJobOrdersForFrontend($filtered);
    }

    private function getValidJobOrderForRating($jobOrderId, $allForm3Ids, $alreadyRatedForm3Ids)
    {
        $jobOrder = JobOrder::where('id', $jobOrderId)
            ->where('status', JobOrderStatus::Completed)
            ->where('serviceable_type', 'form4')
            ->with([
                'serviceable.form3.haulings.haulers.position',
                'serviceable.form3.haulings.haulers.account',
                'serviceable.form3.haulings.drivers.position',
                'serviceable.form3.haulings.drivers.account',

                'serviceable.form3.haulings.assignedPersonnel.teamLeader.position',
                'serviceable.form3.haulings.assignedPersonnel.teamLeader.account',

                'serviceable.form3.haulings.assignedPersonnel.safetyOfficer.position',
                'serviceable.form3.haulings.assignedPersonnel.safetyOfficer.account',
                'serviceable.form3.haulings.assignedPersonnel.teamMechanic.position',
                'serviceable.form3.haulings.assignedPersonnel.teamMechanic.account',
            ])
            ->first();

        if (! $jobOrder || ! $jobOrder->serviceable || ! $jobOrder->serviceable->form3) {
            return null;
        }

        $form3Id = $jobOrder->serviceable->form3->id;

        if (! in_array($form3Id, $allForm3Ids) || in_array($form3Id, $alreadyRatedForm3Ids)) {
            return null;
        }

        return $jobOrder;
    }

    private function getJobOrderWithRatings($jobOrderId, $employee, $allForm3Ids)
    {
        $targetOrder = JobOrder::where('id', $jobOrderId)
            ->where('status', JobOrderStatus::Completed)
            ->where('serviceable_type', 'form4')
            ->with([
                'serviceable.form3',
                'performanceSummary',
                'employeePerformances' => fn ($q) => $q->where('evaluator_id', $employee->id)
                    ->with([
                        'evaluatee.position',
                        'evaluatee.account',
                        'ratings.performanceRating',
                    ]),
            ])
            ->first();

        if (! $targetOrder || ! in_array(optional($targetOrder->serviceable->form3)->id, $allForm3Ids)) {
            return null;
        }

        return $targetOrder;
    }

    // ==================== TEAM MEMBERS & RATINGS METHODS ====================

    private function getTeamMembersToRate($jobOrder, $currentEmployee)
    {
        $form3       = $jobOrder->serviceable->form3;
        $teamMembers = collect();

        foreach ($form3->haulings as $hauling) {
            foreach ($hauling->haulers as $hauler) {
                if ($hauler->id !== $currentEmployee->id) {
                    if (! $hauler->relationLoaded('account')) {
                        $hauler->load('account');
                    }

                    $teamMembers->push([
                        'employee_id'      => $hauler->id,
                        'employee'         => $hauler,
                        'avatar'           => $hauler->account?->avatar,
                        'form3_id'         => $form3->id,
                        'role'             => 'hauler',
                        'job_order_ticket' => $jobOrder->ticket,
                    ]);
                }
            }

            foreach ($hauling->drivers as $driver) {
                if ($driver->id !== $currentEmployee->id) {
                    if (! $driver->relationLoaded('account')) {
                        $driver->load('account');
                    }

                    $teamMembers->push([
                        'employee_id'      => $driver->id,
                        'employee'         => $driver,
                        'avatar'           => $driver->account?->avatar,
                        'form3_id'         => $form3->id,
                        'role'             => 'driver',
                        'job_order_ticket' => $jobOrder->ticket,
                    ]);
                }
            }

            $personnel = $hauling->assignedPersonnel;
            if ($personnel) {
                $roles = [
                    'team_leader'    => $personnel->teamLeader,

                    'safety_officer' => $personnel->safetyOfficer,
                    'team_mechanic'  => $personnel->teamMechanic,
                ];

                foreach ($roles as $role => $person) {
                    if ($person && $person->id !== $currentEmployee->id) {
                        if (! $person->relationLoaded('account')) {
                            $person->load('account');
                        }

                        $teamMembers->push([
                            'employee_id'      => $person->id,
                            'employee'         => $person,
                            'avatar'           => $person->account?->avatar,
                            'form3_id'         => $form3->id,
                            'role'             => $role,
                            'job_order_ticket' => $jobOrder->ticket,
                        ]);
                    }
                }
            }
        }

        return $teamMembers->unique('employee_id')->values();
    }

    private function getRatedTeamMembers($jobOrder)
    {
        return $jobOrder->employeePerformances->map(function ($performance) use ($jobOrder) {
            if (! $performance->evaluatee->relationLoaded('account')) {
                $performance->evaluatee->load('account');
            }

            return [
                'employee_id'      => $performance->evaluatee->id,
                'employee'         => $performance->evaluatee,
                'avatar'           => $performance->evaluatee->account?->avatar,
                'ratings'          => $performance->ratings,
                'role'             => $this->determineEmployeeRole(
                    $jobOrder->serviceable->form3,
                    $performance->evaluatee
                ),
                'job_order_ticket' => $jobOrder->ticket,
            ];
        });
    }

    private function determineEmployeeRole($form3, $employee)
    {
        if (! $form3) {
            return 'unknown';
        }

        foreach ($form3->haulings as $hauling) {
            $personnel = $hauling->assignedPersonnel;
            if ($personnel) {
                if ($personnel->team_leader === $employee->id) {
                    return 'team_leader';
                }

                if ($personnel->safety_officer === $employee->id) {
                    return 'safety_officer';
                }

                if ($personnel->team_mechanic === $employee->id) {
                    return 'team_mechanic';
                }
            }

            if ($hauling->drivers->contains('id', $employee->id)) {
                return 'driver';

            }

            if ($hauling->haulers->contains('id', $employee->id)) {
                return 'hauler';
            }
        }

        return 'unknown';
    }

    // ==================== EMPLOYEE RATINGS TABLE METHODS ====================

    private function getFilteredEmployees($filters)
    {
        $query = Employee::with([
            'position',
            'performancesAsEmployee' => function ($q) {
                $q->whereNull('deleted_at')
                    ->with('ratings.performanceRating');
            },
        ])->whereHas('position', fn ($q) => $q->whereIn('name', self::ALLOWED_POSITIONS));

        if ($filters['positions']) {
            $validPositions = array_intersect($filters['positions'], self::ALLOWED_POSITIONS);
            if ($validPositions) {
                $query->whereHas('position', fn ($q) => $q->whereIn('name', $validPositions));
            }
        }

        if (in_array($filters['sort'], ['name_asc', 'name_desc'])) {
            $this->applySorting($query, $filters['sort']);
        }

        $employees = $query->get();

        $transformedEmployees = $employees->map(function ($employee) {
            $ratings = $employee->performancesAsEmployee
                ->flatMap(fn ($perf) => $perf->ratings)
                ->pluck('performanceRating.scale')
                ->filter();

            $averageRating = $ratings->count() ? round($ratings->avg(), 2) : null;

            $jobOrdersAttended = $this->calculateJobOrdersAttended($employee);

            return (object) [
                'id'                  => $employee->id,
                'full_name'           => $employee->full_name,
                'position'            => $employee->position?->name ?? '',
                'average_rating'      => $averageRating,
                'has_ratings'         => $ratings->count() > 0,
                'total_ratings'       => $ratings->count(),
                'job_orders_attended' => $jobOrdersAttended,
            ];
        });

        return $this->applyPostQueryFilters($transformedEmployees, $filters);
    }

    private function calculateJobOrdersAttended($employee)
    {
        $authorizedForm3Ids = $this->getAuthorizedForm3Ids($employee);

        if (empty($authorizedForm3Ids)) {
            return 0;
        }

        $completedJobOrders = JobOrder::where('status', JobOrderStatus::Completed)
            ->where('serviceable_type', 'form4')
            ->with(['serviceable.form3'])
            ->get();

        $attendedCount = $completedJobOrders->filter(function ($jobOrder) use ($authorizedForm3Ids) {
            $form3Id = optional(optional($jobOrder->serviceable)->form3)->id;

            return $form3Id && in_array($form3Id, $authorizedForm3Ids);
        })->count();

        return $attendedCount;
    }

    private function applySearch($employees, $search)
    {
        $searchTerm = strtolower(trim($search));

        return $employees->filter(function ($employee) use ($searchTerm) {
            return str_contains(strtolower($employee->full_name), $searchTerm) ||
            str_contains(strtolower($employee->position), $searchTerm)         ||
            str_contains((string) $employee->id, $searchTerm);
        })->values();
    }

    private function applyPostQueryFilters($employees, $filters)
    {
        $filtered = $employees;

        if ($filters['evaluation_status']) {
            $filtered = $filtered->filter(function ($emp) use ($filters) {
                $hasRatings = $emp->has_ratings;

                return in_array('no_ratings', $filters['evaluation_status']) ? ! $hasRatings : $hasRatings;
            });
        }

        if ($filters['rating_from'] !== null || $filters['rating_to'] !== null) {
            $filtered = $filtered->filter(function ($emp) use ($filters) {
                $rating = $emp->average_rating;

                if ($rating === null) {
                    return false;
                }

                if ($filters['rating_from'] !== null && $rating < $filters['rating_from']) {
                    return false;
                }

                if ($filters['rating_to'] !== null && $rating > $filters['rating_to']) {
                    return false;
                }

                return true;
            });
        }

        if (in_array($filters['sort'], ['rating_desc', 'rating_asc'])) {
            $direction = $filters['sort'] === 'rating_desc' ? 'sortByDesc' : 'sortBy';
            $filtered  = $filtered->{$direction}(function ($emp) use ($direction) {
                return $emp->average_rating ?? ($direction === 'sortByDesc' ? -1 : 999);
            });
        }

        return $filtered->values();
    }

    // ==================== HISTORY METHODS ====================

    private function getEmployeeRatings($employee, $filters)
    {
        return $employee->performancesAsEmployee->flatMap(function ($perf) {
            return $perf->ratings->map(function ($rating) use ($perf) {
                return [
                    'job_order_id'     => $perf->jobOrder?->id,
                    'job_order_ticket' => $perf->jobOrder?->ticket,
                    'from'             => $perf->evaluator?->full_name       ?? '',
                    'from_position'    => $perf->evaluator?->position?->name ?? '',
                    'scale'            => $rating->performanceRating?->scale ?? null,
                    'description'      => $rating->description,
                    'job_datetime'     => $perf->jobOrder?->date_time ?? $perf->jobOrder?->created_at,
                ];
            });
        })->filter(function ($item) use ($filters) {
            if ($item['scale'] === null) {
                return false;
            }

            $scale = (float) $item['scale'];

            if ($filters['scale_from'] !== null && $scale < $filters['scale_from']) {
                return false;
            }

            if ($filters['scale_to'] !== null && $scale > $filters['scale_to']) {
                return false;
            }

            if ($filters['date_from'] && $item['job_datetime']->lt(Carbon::parse($filters['date_from'])->startOfDay())) {
                return false;
            }

            if ($filters['date_to'] && $item['job_datetime']->gt(Carbon::parse($filters['date_to'])->endOfDay())) {
                return false;
            }

            return true;
        });
    }

    private function sortRatings($ratings, $sort)
    {
        return match ($sort) {
            'date_asc'   => $ratings->sortBy('job_datetime')->values(),
            'date_desc'  => $ratings->sortByDesc('job_datetime')->values(),
            'scale_asc'  => $ratings->sortBy('scale')->values(),
            'scale_desc' => $ratings->sortByDesc('scale')->values(),
            default      => $ratings->values(),
        };
    }

    private function applyHistorySearch($ratings, $search)
    {
        $searchTerm = strtolower(trim($search));

        return $ratings->filter(function ($rating) use ($searchTerm) {
            return str_contains(strtolower($rating['from']), $searchTerm)            ||
            str_contains(strtolower($rating['from_position']), $searchTerm)          ||
            str_contains(strtolower($rating['description'] ?? ''), $searchTerm)      ||
            str_contains((string) $rating['job_order_id'], $searchTerm)              ||
            str_contains(strtolower($rating['job_order_ticket'] ?? ''), $searchTerm) ||
            str_contains((string) $rating['scale'], $searchTerm);
        });
    }

    private function generateHistoryExportFilename($employee, $filters)
    {
        $timestamp    = now()->format('Y-m-d_H-i-s');
        $employeeName = preg_replace('/[^a-zA-Z0-9\s]/', '', $employee->full_name);
        $employeeName = preg_replace('/\s+/', '_', trim($employeeName));

        $filterSuffix = $this->generateHistoryFilterSuffix($filters);

        return "employee_history_{$employeeName}_{$timestamp}{$filterSuffix}.xlsx";
    }

    private function generateHistoryFilterSuffix($filters)
    {
        $parts = [];

        if ($filters['scale_from'] !== null || $filters['scale_to'] !== null) {
            $from    = $filters['scale_from'] ?? 'min';
            $to      = $filters['scale_to']   ?? 'max';
            $parts[] = "rating-{$from}-to-{$to}";
        }

        if ($filters['date_from'] || $filters['date_to']) {
            $from    = $filters['date_from'] ? date('Y-m-d', strtotime($filters['date_from'])) : 'earliest';
            $to      = $filters['date_to'] ? date('Y-m-d', strtotime($filters['date_to'])) : 'latest';
            $parts[] = "date-{$from}-to-{$to}";
        }

        if ($filters['sort']) {
            $sortMap = [
                'date_asc'   => 'date-asc',
                'date_desc'  => 'date-desc',
                'scale_asc'  => 'rating-asc',
                'scale_desc' => 'rating-desc',
            ];
            if (isset($sortMap[$filters['sort']])) {
                $parts[] = 'sort-'.$sortMap[$filters['sort']];
            }
        }

        return $parts ? '_'.implode('_', $parts) : '';
    }

    // ==================== VALIDATION & DATA PROCESSING ====================

    private function validateRatingData(Request $request)
    {
        return $request->validate([
            'ratings'                         => 'required|array|min:1',
            'ratings.*.evaluatee_id'          => 'required|integer|exists:employees,id',
            'ratings.*.job_order_id'          => 'required|integer|exists:job_orders,id',
            'ratings.*.performance_rating_id' => 'required|integer|exists:performance_ratings,id',
            'ratings.*.description'           => 'nullable|string|max:500',
            'overall_remarks'                 => 'nullable|string|max:1000',
        ]);
    }

    private function processRatings($ratings, $employee, $summary)
    {
        foreach ($ratings as $ratingData) {
            $performance = EmployeePerformance::updateOrCreate(
                [
                    'evaluator_id' => $employee->id,
                    'evaluatee_id' => $ratingData['evaluatee_id'],
                    'job_order_id' => $ratingData['job_order_id'],
                ],
                [
                    'performance_summary_id' => $summary->id,
                ]
            );

            EmployeeRating::updateOrCreate(
                [
                    'employee_performance_id' => $performance->id,
                    'performance_category_id' => self::OVERALL_CATEGORY_ID,
                ],
                [
                    'performance_rating_id' => $ratingData['performance_rating_id'],
                    'description'           => $ratingData['description'] ?? null,
                ]
            );
        }
    }

    public function getExportData(Request $request)
    {
        $employee = $this->getAuthenticatedEmployee();
        $this->authorizeConsultant($employee);

        $filters           = $this->parseTableFilters($request);
        $filteredEmployees = $this->getFilteredEmployees($filters);

        if ($search = $request->input('search')) {
            $filteredEmployees = $this->applySearch($filteredEmployees, $search);
        }

        return [
            'data'    => $filteredEmployees,
            'total'   => $filteredEmployees->count(),
            'filters' => $filters,
        ];
    }

    // ==================== FILTER PARSING METHODS ====================

    private function parseIndexFilters(Request $request)
    {
        $filters = $request->get('filters', []);

        return [
            'search'            => $request->get('search'),
            'statuses'          => $filters['statuses']          ?? [],
            'fromDateOfService' => $filters['fromDateOfService'] ?? '',
            'toDateOfService'   => $filters['toDateOfService']   ?? '',
        ];
    }

    private function parseTableFilters(Request $request)
    {
        return [
            'positions'         => $request->input('filter') ? explode(',', $request->input('filter')) : [],
            'evaluation_status' => $request->input('evaluation_status') ? explode(',', $request->input('evaluation_status')) : [],
            'sort'              => $request->input('sort'),
            'rating_from'       => $request->input('rating_from') ? (float) $request->input('rating_from') : null,
            'rating_to'         => $request->input('rating_to') ? (float) $request->input('rating_to') : null,
            'search'            => $request->input('search'),
        ];
    }

    private function parseHistoryFilters(Request $request)
    {
        return [
            'sort'       => $request->input('sort'),
            'scale_from' => $request->input('scale_from') ? (float) $request->input('scale_from') : null,
            'scale_to'   => $request->input('scale_to') ? (float) $request->input('scale_to') : null,
            'date_from'  => $request->input('date_from'),
            'date_to'    => $request->input('date_to'),
        ];
    }

    // ==================== UTILITY METHODS ====================

    private function applySorting($query, $sort)
    {
        $nameOrder = function ($query, $direction) {
            $query->orderBy('first_name', $direction)
                ->orderBy('last_name', $direction)
                ->orderBy('middle_name', $direction)
                ->orderBy('suffix', $direction);
        };

        match ($sort) {
            'name_asc'  => $nameOrder($query, 'asc'),
            'name_desc' => $nameOrder($query, 'desc'),
            default     => $nameOrder($query, 'asc'),
        };
    }

    private function paginateJobOrders($jobOrders, Request $request)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage     = $request->get('per_page', 10);

        $paged = new LengthAwarePaginator(
            $jobOrders->forPage($currentPage, $perPage),
            $jobOrders->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'data' => $paged->items(),
            'meta' => [
                'current_page' => $paged->currentPage(),
                'last_page'    => $paged->lastPage(),
                'per_page'     => $paged->perPage(),
                'total'        => $paged->total(),
            ],
        ];
    }

    private function paginateEmployees($employees, Request $request)
    {
        $perPage     = min($request->integer('per_page', 10), 50);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $employees->forPage($currentPage, $perPage),
            $employees->count(),
            $perPage,
            $currentPage,
            [
                'path'  => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    private function paginateRatings($ratings, Request $request)
    {
        $perPage = min($request->integer('per_page', 10), 50);
        $current = LengthAwarePaginator::resolveCurrentPage();

        $paged = $ratings
            ->forPage($current, $perPage)
            ->map(function ($row) {
                $row['date'] = Carbon::parse($row['job_datetime'])->format('d F Y h:i A');
                unset($row['job_datetime']);

                return $row;
            })
            ->values();

        return [
            'data' => $paged,
            'meta' => [
                'current_page' => $current,
                'last_page'    => ceil($ratings->count() / $perPage),
                'per_page'     => $perPage,
                'total'        => $ratings->count(),
            ],
        ];
    }

    private function generateExportFilename($filters)
    {
        $timestamp    = now()->format('Y-m-d_H-i-s');
        $filterSuffix = $this->generateFilterSuffix($filters);

        return "employee_ratings_export_{$timestamp}{$filterSuffix}.xlsx";
    }

    private function generateFilterSuffix($filters)
    {
        $parts = [];

        if ($filters['positions']) {
            $parts[] = 'pos-'.implode('-', array_map('strtolower', $filters['positions']));
        }

        if ($filters['evaluation_status']) {
            $parts[] = 'eval-'.implode('-', $filters['evaluation_status']);
        }

        if ($filters['rating_from'] !== null || $filters['rating_to'] !== null) {
            $from    = $filters['rating_from'] ?? 'min';
            $to      = $filters['rating_to']   ?? 'max';
            $parts[] = "rating-{$from}-to-{$to}";
        }

        if ($filters['search']) {
            $parts[] = 'search-'.preg_replace('/[^a-zA-Z0-9]/', '', substr($filters['search'], 0, 10));
        }

        return $parts ? '_'.implode('_', $parts) : '';
    }
}
