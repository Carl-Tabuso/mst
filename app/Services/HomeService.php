<?php

namespace App\Services;

use App\Enums\HaulingStatus;
use App\Enums\JobOrderCorrectionRequestStatus;
use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Enums\UserRole;
use App\Http\Resources\ActivityLogResource;
use App\Models\Employee;
use App\Models\Form4;
use App\Models\Form5;
use App\Models\ITService;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Facades\Agent;
use Spatie\Activitylog\Models\Activity;

class HomeService
{
    public function getCurrentHour(): array
    {
        $hour = now()->hour;

        $currentHour = [
            'dayPart'      => '',
            'illustration' => '',
        ];

        if ($hour < 12) {
            $currentHour['dayPart']      = 'morning';
            $currentHour['illustration'] = 'greetings/morning-illustration.png';
        } elseif ($hour < 18) {
            $currentHour['dayPart']      = 'afternoon';
            $currentHour['illustration'] = 'greetings/afternoon-illustration.png';
        } else {
            $currentHour['dayPart']      = 'evening';
            $currentHour['illustration'] = 'greetings/evening-illustration.png';
        }

        return $currentHour;
    }

    // public function someFunc()
    // {
    //     $x = [
    //         UserRole::HeadFrontliner->value = [
    //             'component' => '1',
    //             'data' => [
    //                 'latestFromJobOrderCards'   => fn () => $this->getMonthlyJobOrderCounts(),
    //                 'recentActivities'          => fn () => $this->getRecentActivities(),
    //                 'employeeMetrics'           => fn () => $this->getEmployeeStatusCounts(),
    //                 'recentJobOrders'           => fn () => $this->getRecentJobOrders(),
    //                 'awaitingCorrectionReviews' => fn () => $this->getRecentJobOrderCorrectionRequests(),
    //                 'agingJobOrderTickets'      => fn () => $this->getAgingJobOrders(),
    //             ],
    //         ],
    //         UserRole::ITAdmin->value = [
    //             'component' => '2',
    //             'data' => [
    //                 'userStatistics'   => fn () => $this->getUserStatistics(),
    //                 'recentActivities' => fn () => $this->getRecentActivities(),
    //             ],
    //         ],
    //         UserRole::TeamLeader->value = [
    //             'component' => '3',
    //             'data' => [
    //                 'recentActivities' => fn () => $this->getUserRecentActivities(),
    //             ],
    //         ],
    //         UserRole::Consultant->value = [
    //             'component' => '4',
    //             'data' => [
    //                 'latestFromJobOrderCards' => fn () => $this->getMonthlyJobOrderCounts(),
    //                 'employeeMetrics'         => fn () => $this->getEmployeeStatusCounts(),
    //                 'recentActivities'        => fn () => $this->getUserRecentActivities(),
    //             ],
    //         ],
    //         UserRole::HumanResource->value = [
    //             'component' => '5',
    //             'data' => [
    //                 'employeeMetrics'  => fn () => $this->getEmployeeStatusCounts(),
    //                 'recentActivities' => fn () => $this->getUserRecentActivities(),
    //             ],
    //         ],
    //         'default' => [
    //             'component' => '6',
    //             'data' => [
    //                 'recentActivities' => fn () => $this->getUserRecentActivities(),
    //             ],
    //         ],
    //     ];
    // }

    public function getComponent(): string
    {
        $role = UserRole::from(request()->user()->getRoleNames()->first());

        $subFolder = match ($role) {
            UserRole::HeadFrontliner => 'head-frontliner',
            UserRole::ITAdmin        => 'it-admin',
            UserRole::TeamLeader     => 'team-leader',
            UserRole::Consultant     => 'consultant',
            UserRole::HumanResource  => 'human-resource',
            UserRole::Dispatcher     => 'dispatcher',
            UserRole::Frontliner     => 'frontliner',
            default                  => 'regular',
        };

        return sprintf('%s/%s/%s', 'home', $subFolder, 'Index');
    }

    public function getUserHomeData(): array
    {
        $role = UserRole::from(request()->user()->getRoleNames()->first());

        if ($role === UserRole::HeadFrontliner) {
            return [
                'latestFromJobOrderCards'   => $this->getMonthlyJobOrderCounts(),
                'recentActivities'          => $this->getRecentActivities(),
                'employeeMetrics'           => $this->getEmployeeStatusCounts(),
                'recentJobOrders'           => $this->getRecentJobOrders(),
                'awaitingCorrectionReviews' => $this->getRecentJobOrderCorrectionRequests(),
                'agingJobOrderTickets'      => $this->getAgingJobOrders(),
            ];
        } elseif ($role === UserRole::ITAdmin) {
            return [
                'userStatistics'   => $this->getUserStatistics(),
                'recentActivities' => $this->getRecentActivities(),
            ];
        } elseif ($role === UserRole::TeamLeader) {
            return [
                'recentActivities'         => $this->getUserRecentActivities(),
                'currentYearParticipation' => $this->getCurrentYearParticipation(),
                'awaitingSafetyInspection' => $this->getJobOrdersAwaitingSafetyInspection(),
            ];
        } elseif ($role === UserRole::Consultant) {
            return [
                'latestFromJobOrderCards' => $this->getMonthlyJobOrderCounts(),
                'employeeMetrics'         => $this->getEmployeeStatusCounts(),
                'recentActivities'        => $this->getUserRecentActivities(),
            ];
        } elseif ($role === UserRole::HumanResource) {
            return [
                'employeeMetrics'  => $this->getEmployeeStatusCounts(),
                'recentActivities' => $this->getUserRecentActivities(),
            ];
        } elseif ($role === UserRole::Dispatcher) {
            return [
                'recentActivities'            => $this->getUserRecentActivities(),
                'currentYearParticipation'    => $this->getCurrentYearParticipation(),
                'awaitingPersonnelAssignment' => $this->getJobOrdersAwaitingPersonnelAssignment(),
            ];
        } elseif ($role === UserRole::Frontliner) {
            return [
                'recentActivities'             => $this->getUserRecentActivities(),
                'createdJobOrderStatistics'    => $this->getFrontlinerCreatedJobOrderStatistics(),
                'jobOrderCorrectionStatistics' => $this->getFrontlinerJobOrderCorrectionStatistics(),
            ];
        } else {
            return [
                'recentActivities'         => $this->getUserRecentActivities(),
                'currentYearParticipation' => $this->getCurrentYearParticipation(),
            ];
        }
    }

    public function getEmployeeStatusCounts(): array
    {
        return Employee::query()
            ->withTrashed()
            ->with('account')
            ->get()
            ->pipe(fn (Collection $employees) => [
                [
                    'status' => 'Active',
                    'total'  => $employees->whereNull('archived_at')->count(),
                ],
                [
                    'status' => 'Inactive',
                    'total'  => $employees->whereNotNull('archived_at')->count(),
                ],
                [
                    'status' => 'No Account',
                    'total'  => $employees->where('account', null)->count(),
                ],
            ]);
    }

    public function getMonthlyJobOrderCounts(): mixed
    {
        $order = [
            JobOrderServiceType::Form4->value     => 1,
            JobOrderServiceType::ITService->value => 2,
            JobOrderServiceType::Form5->value     => 3,
        ];

        return JobOrder::query()
            ->fromPastMonth()
            ->get()
            ->groupByServiceType()
            ->sortKeysUsing(fn ($first, $second) => $order[$first] <=> $order[$second])
            ->map(fn ($jobOrders, $serviceType) => [
                'serviceType' => JobOrderServiceType::from($serviceType)->getLabel(),
                'total'       => $jobOrders->count(),
            ])
            ->values();
    }

    public function getRecentActivities(): Collection
    {
        $activities = Activity::query()
            ->with([
                'causer' => [
                    'employee' => [
                        'account',
                    ],
                    'roles',
                ],
            ])
            ->latest()
            ->paginate(10);

        $activityCollection = ActivityLogResource::collection($activities->items());

        return $activityCollection->collection;
    }

    public function getRecentJobOrders(): Collection
    {
        return JobOrder::query()
            ->with(['creator'])
            ->fromPastWeek()
            ->take(10)
            ->get()
            ->map(fn (JobOrder $jobOrder) => [
                'ticket'      => $jobOrder->ticket,
                'serviceType' => $jobOrder->serviceable_type,
                'status'      => $jobOrder->status,
                'humanDiff'   => $jobOrder->date_time->diffForHumans(),
                'frontliner'  => $jobOrder->creator->full_name,
            ]);
    }

    public function getRecentJobOrderCorrectionRequests(): Collection
    {
        return JobOrderCorrection::query()
            ->where('status', JobOrderCorrectionRequestStatus::Pending)
            ->with(['jobOrder' => ['creator']])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (JobOrderCorrection $correction) => [
                'id'           => $correction->id,
                'ticket'       => $correction->jobOrder->ticket,
                'serviceType'  => $correction->jobOrder->serviceable_type,
                'requestedAt'  => $correction->created_at->format('M d'),
                'changesCount' => count($correction->properties['after']),
                'requestedBy'  => $correction->jobOrder->creator->full_name,
            ]);
    }

    public function getAgingJobOrders(): Collection
    {
        $concernStatuses = [
            JobOrderStatus::ForViewing,
            JobOrderStatus::ForProposal,
            JobOrderStatus::ForApproval,
            JobOrderStatus::Successful,
            JobOrderStatus::PreHauling,
        ];

        return JobOrder::query()
            ->with('creator')
            ->latest()
            ->updatedPastWeekOrMore()
            ->ofStatuses($concernStatuses)
            ->take(10)
            ->get()
            ->map(fn (JobOrder $jobOrder) => [
                'ticket'      => $jobOrder->ticket,
                'serviceType' => $jobOrder->serviceable_type,
                'status'      => $jobOrder->status,
                'lastUpdated' => $jobOrder->updated_at->format('M d'),
                'frontliner'  => $jobOrder->creator->full_name,
            ]);
    }

    public function getUserStatistics(): array
    {
        $totalUser     = User::withTrashed()->count();
        $totalEmployee = Employee::withTrashed()->count();

        return User::query()
            ->withTrashed()
            ->get()
            ->pipe(fn (Collection $users) => [
                [
                    'label' => 'Total Users',
                    'total' => $totalUser,
                ],
                [
                    'label' => 'Employee w No Account',
                    'total' => $totalEmployee - $totalUser,
                ],
                [
                    'label' => 'Active Users',
                    'total' => $users->active()->count(),
                ],
                [
                    'label' => 'Inactive Users',
                    'total' => $users->inactive()->count(),
                ],
            ]);
    }

    public function getUserRecentActivities(): Collection
    {
        $user = request()->user();

        return Activity::query()
            ->where('causer_type', $user->getMorphClass())
            ->where('causer_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (Activity $activity) => [
                'description' => $activity->description,
                'ipAddress'   => $activity->properties['ip_address'],
                'browser'     => Agent::browser($activity->properties['user_agent']),
                'platform'    => Agent::platform($activity->properties['user_agent']),
                'timestamp'   => $activity->created_at->diffForHumans(),
            ]);
    }

    public function getJobOrdersAwaitingPersonnelAssignment()
    {
        $x = JobOrder::query()
            ->whereHasMorph('serviceable', [Form4::class], function (Builder $query) {
                $query->whereHas('form3.haulings', function (Builder $subQuery) {
                    $subQuery->where('status', HaulingStatus::ForPersonnelAssignment);
                });
            })
            ->latest()
            ->get();

        // dd($x);
    }

    public function getJobOrdersAwaitingSafetyInspection()
    {
        $x = JobOrder::query()
            ->whereHasMorph('serviceable', [Form4::class], function (Builder $query) {
                $query->whereHas('form3.haulings', function (Builder $subQuery) {
                    $subQuery
                        ->whereHas('assignedPersonnel', function (Builder $ssubQuery) {
                            $ssubQuery->where('team_leader', request()->user()->employee_id);
                        })
                        ->where('status', HaulingStatus::ForSafetyInspection);
                });
            })
            ->latest()
            ->get();

        dd($x);
    }

    public function getCurrentYearParticipation(): Collection
    {
        $employeeId = request()->user()->employee_id;

        $currentYear = today()->year;

        $monthAndServiceTypesTotal = JobOrder::query()
            ->withTrashed()
            ->whereYear('date_time', $currentYear)
            ->whereHasMorph('serviceable', [
                Form4::class,
                ITService::class,
                Form5::class,
            ], function (Builder $query) use ($employeeId) {
                $currentModel = $query->getModel();

                if ($currentModel instanceof Form4) {
                    $this->applyWasteManagementInvolvementFilter($query, $employeeId);
                }

                if ($currentModel instanceof ITService) {
                    //
                }

                if ($currentModel instanceof Form5) {
                    //
                }
            })
            ->select('serviceable_type')
            ->selectRaw('count(*) as total')
            ->selectRaw('monthname(date_time) as month')
            ->groupBy('serviceable_type')
            ->groupByRaw('monthname(date_time)')
            ->get()
            ->groupBy(fn (JobOrder $value) => $value->month)
            ->map(function (Collection $group, string $month) {
                $wm  = $group->firstWhere('serviceable_type', JobOrderServiceType::Form4)->total;
                $its = $group->firstWhere('serviceable_type', JobOrderServiceType::ITService)->total;
                $os  = $group->firstWhere('serviceable_type', JobOrderServiceType::Form5)->total;

                return [
                    'month'                                    => $month,
                    JobOrderServiceType::Form4->getLabel()     => $wm,
                    JobOrderServiceType::ITService->getLabel() => $its,
                    JobOrderServiceType::Form5->getLabel()     => $os,
                ];
            })
            ->values();

        return $monthAndServiceTypesTotal;
    }

    private function applyWasteManagementInvolvementFilter($query, $employeeId): Builder
    {
        return $query->where('form_dispatcher', $employeeId)
            ->orWhere(function (Builder $subQuery) use ($employeeId) {
                $subQuery
                    ->whereHas('appraisers', function (Builder $ssubQuery) use ($employeeId) {
                        $ssubQuery->where('employee_id', $employeeId);
                    })
                    ->orWhereHas('form3.haulings.assignedPersonnel', function (Builder $ssubQuery) use ($employeeId) {
                        $ssubQuery->where('team_leader', $employeeId);
                    })
                    ->orWhereHas('form3.haulings.haulers', function (Builder $ssubQuery) use ($employeeId) {
                        $ssubQuery->where('hauler', $employeeId);
                    });
            });
    }

    public function getFrontlinerCreatedJobOrderStatistics(): array
    {
        $currentYear = today()->year;

        $employeeId = request()->user()->employee_id;

        return JobOrder::query()
            ->withTrashed()
            ->whereYear('created_at', $currentYear)
            ->where('created_by', $employeeId)
            ->get()
            ->pipe(fn (Collection $jobOrders) => [
                [
                    'serviceType' => JobOrderServiceType::Form4->getLabel(),
                    'total'       => $jobOrders->where('serviceable_type', JobOrderServiceType::Form4)->count(),
                ],
                [
                    'serviceType' => JobOrderServiceType::ITService->getLabel(),
                    'total'       => $jobOrders->where('serviceable_type', JobOrderServiceType::ITService)->count(),
                ],
                [
                    'serviceType' => JobOrderServiceType::Form5->getLabel(),
                    'total'       => $jobOrders->where('serviceable_type', JobOrderServiceType::Form5)->count(),
                ],
            ]);
    }

    public function getFrontlinerJobOrderCorrectionStatistics(): array
    {
        $currentYear = today()->year;

        $employeeId = request()->user()->employee_id;

        return JobOrderCorrection::query()
            ->withTrashed()
            ->whereYear('created_at', $currentYear)
            ->whereHas('jobOrder', fn (Builder $query) => $query->where('created_by', $employeeId))
            ->get()
            ->pipe(function (Collection $corrections) {
                $approved = JobOrderCorrectionRequestStatus::Approved;
                $pending  = JobOrderCorrectionRequestStatus::Pending;
                $rejected = JobOrderCorrectionRequestStatus::Rejected;

                return [
                    [
                        'status' => $approved->getLabel(),
                        'total'  => $corrections->where('status', $approved)->count(),
                    ],
                    [
                        'status' => $pending->getLabel(),
                        'total'  => $corrections->where('status', $pending)->count(),
                    ],
                    [
                        'status' => $rejected->getLabel(),
                        'total'  => $corrections->where('status', $rejected)->count(),
                    ],
                ];
            });
    }
}
