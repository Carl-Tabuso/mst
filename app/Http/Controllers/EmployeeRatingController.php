<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Models\Employee;
use App\Models\EmployeePerformance;
use App\Models\EmployeeRating;
use App\Models\PerformanceSummary;
use App\Models\Form3;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;


class EmployeeRatingController extends Controller
{
    public function index(Request $request)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage     = 10;

        $user     = auth()->user();
        $employee = $user->employee()->with('position')->first();

        if (! $employee || ! $employee->position) {
            abort(403, 'Unauthorized');
        }

        $allowedPositions     = ['Driver', 'Hauler', 'Team Leader'];
        $employeePositionName = strtolower($employee->position->name ?? '');

        $allowed = collect($allowedPositions)
            ->map(fn($pos) => strtolower($pos))
            ->contains($employeePositionName);

        if (! $allowed) {
            abort(403, 'Unauthorized');
        }

        $personnelForm3Ids = Form3::query()
            ->where(function ($q) use ($employee) {
                $q->where('team_leader', $employee->id)
                    ->orWhere('team_driver', $employee->id);
            })
            ->pluck('id')
            ->toArray();

        $haulerForm3Ids = $employee->form3sHauler()->pluck('form3.id')->toArray();

        $allForm3Ids = array_unique(array_merge($haulerForm3Ids, $personnelForm3Ids));

        // Exclude already rated
        $alreadyRatedForm3Ids = EmployeePerformance::where('evaluator_id', $employee->id)
            ->whereHas('jobOrder', function ($q) {
                $q->where('serviceable_type', 'form4');
            })
            ->get()
            ->map(fn($perf) => optional($perf->jobOrder->serviceable->form3)->id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $completedJobOrders = JobOrder::where('status', JobOrderStatus::Completed)
            ->where('serviceable_type', 'form4')
            ->with([
                'serviceable',
                'serviceable.form3',
                'serviceable.form3.haulers.position',
                'serviceable.form3.teamLeader.position',
                'serviceable.form3.driver.position',
            ])
            ->latest()
            ->get();

        $filtered = $completedJobOrders->filter(function ($jobOrder) use ($allForm3Ids) {
            $form3Id = optional(optional($jobOrder->serviceable)->form3)->id;
            return $form3Id && in_array($form3Id, $allForm3Ids);
        })->values();

        $filtered = $filtered->map(function ($jobOrder) use ($alreadyRatedForm3Ids) {
            $form3Id                 = optional(optional($jobOrder->serviceable)->form3)->id;
            $jobOrder->rating_status = in_array($form3Id, $alreadyRatedForm3Ids)
                ? 'Evaluation Done'
                : 'To be Evaluated';
            return $jobOrder;
        });

        $paged = new LengthAwarePaginator(
            $filtered->forPage($currentPage, $perPage),
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return Inertia::render('ratings/pages/Index', [
            'jobOrders'  => [
                'data' => $paged->items(),
                'meta' => [
                    'current_page' => $paged->currentPage(),
                    'last_page'    => $paged->lastPage(),
                    'per_page'     => $paged->perPage(),
                    'total'        => $paged->total(),
                ],
            ],
            'employeeId' => $employee->id,
        ]);
    }

    public function create(Request $request)
    {
        $user     = auth()->user();
        $employee = $user->employee()->with('position')->first();

        if (! $employee || ! $employee->position) {
            abort(403, 'Unauthorized');
        }

        $allowedPositions     = ['Driver', 'Hauler', 'Team Leader'];
        $employeePositionName = strtolower($employee->position->name ?? '');
        $allowed              = collect($allowedPositions)->map(fn($pos) => strtolower($pos))->contains($employeePositionName);

        if (! $allowed) {
            abort(403, 'Unauthorized');
        }

        $personnelForm3Ids = Form3::query()
            ->where(function ($q) use ($employee) {
                $q->where('team_leader', $employee->id)
                    ->orWhere('team_driver', $employee->id);
            })
            ->pluck('id')
            ->toArray();

        $haulerForm3Ids = $employee->form3sHauler()->pluck('form3.id')->toArray();
        $allForm3Ids    = array_unique(array_merge($haulerForm3Ids, $personnelForm3Ids));

        $alreadyRatedForm3Ids = EmployeePerformance::where('evaluator_id', $employee->id)
            ->whereHas('jobOrder', fn($q) => $q->where('serviceable_type', 'form4'))
            ->get()
            ->map(fn($perf) => optional($perf->jobOrder->serviceable->form3)->id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $completedJobOrders = JobOrder::where('status', JobOrderStatus::Completed)
            ->where('serviceable_type', 'form4')
            ->with([
                'serviceable',
                'serviceable.form3',
                'serviceable.form3.haulers.position',
                'serviceable.form3.teamLeader.position',
                'serviceable.form3.driver.position',
            ])
            ->get();

        $filtered = $completedJobOrders->filter(function ($jobOrder) use ($allForm3Ids, $alreadyRatedForm3Ids) {
            $form4 = $jobOrder->serviceable;
            if (! $form4 || ! $form4->form3) {
                return false;
            }

            $form3Id = $form4->form3->id;
            return in_array($form3Id, $allForm3Ids) && ! in_array($form3Id, $alreadyRatedForm3Ids);
        });

        $jobOrderId = $request->input('job_order_id');

        if (! $jobOrderId) {
            return redirect()->route('employee.ratings.index')->with('error', 'Job order ID is required.');
        }

        $filtered = $filtered->filter(fn($jo) => $jo->id == $jobOrderId);

        if ($filtered->isEmpty()) {
            return redirect()->route('employee.ratings.index')->with('error', 'Invalid or unauthorized job order.');
        }

        return Inertia::render('ratings/pages/EmployeeRating', [
            'jobOrders'  => $filtered->values(),
            'employeeId' => $employee->id,
        ]);
    }

    public function view(Request $request)
    {
        $user     = auth()->user();
        $employee = $user->employee()->with('position')->first();

        if (! $employee || ! $employee->position) {
            abort(403, 'Unauthorized');
        }

        $allowedPositions     = ['Driver', 'Hauler', 'Team Leader'];
        $employeePositionName = strtolower($employee->position->name ?? '');
        $allowed              = collect($allowedPositions)
            ->map(fn($pos) => strtolower($pos))
            ->contains($employeePositionName);

        if (! $allowed) {
            abort(403, 'Unauthorized');
        }

        $personnelForm3Ids = Form3::query()
            ->where(
                fn($q) => $q
                    ->where('team_leader', $employee->id)
                    ->orWhere('team_driver', $employee->id)
            )
            ->pluck('id')
            ->toArray();

        $haulerForm3Ids = $employee->form3sHauler()->pluck('form3.id')->toArray();
        $allForm3Ids    = array_unique(array_merge($haulerForm3Ids, $personnelForm3Ids));

        $jobOrderId = $request->input('job_order_id');
        if (! $jobOrderId) {
            return redirect()->route('employee.ratings.index')->with('error', 'Job order ID is required.');
        }

        $targetOrder = JobOrder::where('id', $jobOrderId)
            ->where('status', JobOrderStatus::Completed)
            ->where('serviceable_type', 'form4')
            ->with([
                'serviceable',
                'performanceSummary',

                'employeePerformances' => fn($q) =>
                $q->where('evaluator_id', $employee->id)
                    ->with('ratings.performanceRating')
                    ->select([
                        'id',
                        'job_order_id',
                        'evaluator_id',
                    ]),

                'serviceable.form3'    => function ($query) use ($employee) {
                    $query->with([
                        'teamLeader.position',
                        'driver.position',
                        'haulers.position',
                        'teamLeader.performancesAsEmployee' => fn($q) =>
                        $q->where('evaluator_id', $employee->id)
                            ->with('ratings.performanceRating'),
                        'driver.performancesAsEmployee'     => fn($q)     =>
                        $q->where('evaluator_id', $employee->id)
                            ->with('ratings.performanceRating'),
                        'haulers.performancesAsEmployee'    => fn($q)    =>
                        $q->where('evaluator_id', $employee->id)
                            ->with('ratings.performanceRating'),
                    ]);
                },
            ])
            ->first();

        if (! $targetOrder || ! in_array(optional($targetOrder->serviceable->form3)->id, $allForm3Ids)) {
            return redirect()->route('employee.ratings.index')->with('error', 'Invalid or unauthorized job order.');
        }

        Log::info('Viewing ratings for job order ID: ' . $targetOrder->id . ' by employee ID: ' . $employee->id);

        return Inertia::render('ratings/pages/ViewEmployeeRating', [
            'jobOrders'  => collect([$targetOrder]),
            'employeeId' => $employee->id,
        ]);
    }

    public function store(Request $request)
    {
        $user     = auth()->user();
        $employee = $user->employee;

        $data = $request->validate([
            'ratings'                         => 'required|array',
            'ratings.*.evaluatee_id'          => 'required|integer|exists:employees,id',
            'ratings.*.job_order_id'          => 'required|integer|exists:job_orders,id',
            'ratings.*.performance_rating_id' => 'required|integer|exists:performance_ratings,id',
            'ratings.*.description'           => 'nullable|string|max:255',
            'overall_remarks'                 => 'nullable|string|max:1000',
        ]);

        $overallCategoryId = 1;

        $jobOrderId = $data['ratings'][0]['job_order_id'];

        $summary = PerformanceSummary::updateOrCreate(
            ['job_order_id' => $jobOrderId],
            ['overall_remarks' => $data['overall_remarks'] ?? null]
        );

        foreach ($data['ratings'] as $rating) {
            $performance = EmployeePerformance::updateOrCreate(
                [
                    'evaluator_id' => $employee->id,
                    'evaluatee_id' => $rating['evaluatee_id'],
                    'job_order_id' => $rating['job_order_id'],
                ],
                [
                    'performance_summary_id' => $summary->id,
                ]
            );

            EmployeeRating::updateOrCreate(
                [
                    'employee_performance_id' => $performance->id,
                    'performance_category_id' => $overallCategoryId,
                ],
                [
                    'performance_rating_id' => $rating['performance_rating_id'],
                    'description'           => $rating['description'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Ratings and overall remarks submitted!');
    }

    public function ratingsTable(Request $request)
    {
        $employee = auth()->user()->employee()->with('position')->first();

        if ($employee->position->name !== 'Consultant') {
            abort(403, 'Unauthorized');
        }

        $perPage = $request->input('per_page', 10);
        $filter = $request->input('filter');
        $sort = $request->input('sort');
        $evaluationStatus = $request->input('evaluation_status');
        $ratingFrom = $request->input('rating_from');
        $ratingTo = $request->input('rating_to');

        $allowedPositions = [
            'Team Leader',
            'Driver',
            'Hauler',
        ];

        $query = Employee::with(['position', 'performancesAsEmployee.ratings.performanceRating'])
            ->whereHas('position', function ($q) use ($allowedPositions) {
                $q->whereIn('name', $allowedPositions);
            });

        // Position filter
        $filterArray = $filter ? explode(',', $filter) : [];
        if ($filterArray && count($filterArray)) {
            $query->whereHas('position', function ($q) use ($filterArray, $allowedPositions) {
                $q->whereIn('name', array_intersect($filterArray, $allowedPositions));
            });
        }

        $orderBy = function ($query, $direction) {
            $query->orderBy('first_name', $direction)
                ->orderBy('last_name', $direction)
                ->orderBy('middle_name', $direction)
                ->orderBy('suffix', $direction);
        };

        if ($sort === 'name_asc') {
            $orderBy($query, 'asc');
        } elseif ($sort === 'name_desc') {
            $orderBy($query, 'desc');
        }

        $employees = $query->paginate($perPage);

        $employees->getCollection()->transform(function ($emp) {
            $allRatings = $emp->performancesAsEmployee
                ->filter(fn($perf) => $perf->deleted_at === null)
                ->flatMap(fn($perf) => $perf->ratings)
                ->pluck('performanceRating.scale')
                ->filter();

            $average = $allRatings->count() ? round($allRatings->avg(), 2) : null;

            return [
                'id' => $emp->id,
                'full_name' => $emp->full_name,
                'position' => $emp->position?->name ?? '',
                'average_rating' => $average,
                'has_ratings' => $allRatings->count() > 0,
            ];
        });

        // Apply evaluation status filter after transformation
        $evaluationStatusArray = $evaluationStatus ? explode(',', $evaluationStatus) : [];
        if ($evaluationStatusArray && count($evaluationStatusArray)) {
            $employees->setCollection(
                $employees->getCollection()->filter(function ($emp) use ($evaluationStatusArray) {
                    $hasRatings = $emp['has_ratings'];

                    $matchesFilter = false;

                    if (in_array('no_ratings', $evaluationStatusArray) && !$hasRatings) {
                        $matchesFilter = true;
                    }

                    return $matchesFilter;
                })->values()
            );
        }

        // Apply rating range filter after transformation
        if ($ratingFrom !== null || $ratingTo !== null) {
            $employees->setCollection(
                $employees->getCollection()->filter(function ($emp) use ($ratingFrom, $ratingTo) {
                    $rating = $emp['average_rating'];

                    // Skip employees with no ratings for range filtering
                    if ($rating === null) {
                        return false;
                    }

                    $matchesRange = true;

                    if ($ratingFrom !== null && $rating < (float)$ratingFrom) {
                        $matchesRange = false;
                    }

                    if ($ratingTo !== null && $rating > (float)$ratingTo) {
                        $matchesRange = false;
                    }

                    return $matchesRange;
                })->values()
            );
        }

        if ($sort === 'rating_desc') {
            $employees->setCollection(
                $employees->getCollection()->sortByDesc(function ($emp) {
                    return $emp['average_rating'] ?? -1;
                })->values()
            );
        } elseif ($sort === 'rating_asc') {
            $employees->setCollection(
                $employees->getCollection()->sortBy(function ($emp) {
                    return $emp['average_rating'] ?? 999;
                })->values()
            );
        }

        $employees->getCollection()->transform(function ($emp) {
            unset($emp['has_ratings']);
            return $emp;
        });

        $filteredCount = $employees->getCollection()->count();
        $employees->setCollection($employees->getCollection());

        return inertia('ratings/pages/RatingTable', [
            'employees' => $employees,
            'allowedPositions' => [
                'Driver',
                'Hauler',
                'Team Leader',
            ],
        ]);
    }

    public function historyPage(Request $request, $employeeId)
    {
        $employee = auth()->user()->employee()->with('position')->first();

        if ($employee->position->name !== 'Consultant') {
            abort(403, 'Unauthorized');
        }

        $employee = Employee::with([
            'performancesAsEmployee.jobOrder',
            'performancesAsEmployee.ratings.performanceRating',
            'performancesAsEmployee.evaluator.position',
            'position',
        ])->findOrFail($employeeId);

        $sort = $request->input('sort');
        $scaleFrom = $request->input('scale_from');
        $scaleTo = $request->input('scale_to');
        $dateFrom   = $request->input('date_from'); 
        $dateTo     = $request->input('date_to');

        $received = $employee->performancesAsEmployee->flatMap(function ($perf) {
            return $perf->ratings->map(function ($rating) use ($perf) {
                $jobOrder = $perf->jobOrder;
        
                return [
                    'job_order_id'  => $jobOrder?->id,
                    'from'          => $perf->evaluator?->full_name ?? '',
                    'from_position' => $perf->evaluator?->position?->name ?? '',
                    'scale'         => $rating->performanceRating?->scale ?? null,
                    'description'   => $rating->description,
                    'job_datetime'  => $jobOrder?->date_time ?? $jobOrder?->created_at,
                ];
            });
        })->filter(function ($item) use ($scaleFrom, $scaleTo, $dateFrom, $dateTo) {
            if ($item['scale'] === null) return false;

            $scale = (float) $item['scale'];
            if (!is_null($scaleFrom) && $scale < $scaleFrom) return false;
            if (!is_null($scaleTo) && $scale > $scaleTo) return false;

            if ($dateFrom) {
                if ($item['job_datetime']->lt(Carbon::parse($dateFrom)->startOfDay())) return false;
            }
            if ($dateTo) {
                if ($item['job_datetime']->gt(Carbon::parse($dateTo)->endOfDay()))   return false;
            }

            return true;
        });

        switch ($sort) {
            case 'date_asc':
                $received = $received->sortBy('job_datetime')->values();
                break;
            case 'date_desc':
                $received = $received->sortByDesc('job_datetime')->values();
                break;
            case 'scale_asc':
                $received = $received->sortBy('scale')->values();
                break;
            case 'scale_desc':
                $received = $received->sortByDesc('scale')->values();
                break;
            default:
                $received = $received->values();
        }     

        $perPage = $request->integer('per_page', 10);
        $current = LengthAwarePaginator::resolveCurrentPage();

        $paged = $received
            ->forPage($current, $perPage)
            ->map(function ($row) {
                $row['date'] = Carbon::parse($row['job_datetime'])
                    ->format('d F Y h:i A');
                unset($row['job_datetime']);
                return $row;
            })
            ->values();

        $ratingsPaginator = new LengthAwarePaginator(
                items: $paged,
                total: $received->count(),
                perPage: $perPage,
                currentPage: $current,
                options: [
                    'path' => url()->current(),
                    'query' => $request->query(),
                ]
            );

        return Inertia::render('ratings/pages/RatingHistory', [
            'employee' => [
                'id'        => $employee->id,
                'full_name' => $employee->full_name,
                'position'  => $employee->position->name ?? '',
            ],
            'received' => [
                'data' => $ratingsPaginator->items(),
                'meta' => [
                    'current_page' => $ratingsPaginator->currentPage(),
                    'last_page'    => $ratingsPaginator->lastPage(),
                    'per_page'     => $ratingsPaginator->perPage(),
                    'total'        => $ratingsPaginator->total(),
                ],
            ],
            'filters'  => [
                'sort'       => $sort,
                'scale_from' => $scaleFrom,
                'scale_to'   => $scaleTo,
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
            ],
        ]);                
    }

    public function archive(Request $request)
    {
        $employeeIds = $request->input('ids', []);

        if (!is_array($employeeIds) || empty($employeeIds)) {
            return back()->with('error', 'No employees selected.');
        }

        $performances = EmployeePerformance::whereIn('evaluatee_id', $employeeIds)->get();

        foreach ($performances as $performance) {
            $performance->delete();
        }

        return back()->with('success', 'Selected employee evaluations archived successfully.');
    }
}
