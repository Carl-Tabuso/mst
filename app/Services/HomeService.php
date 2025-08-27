<?php

namespace App\Services;

use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Enums\UserRole;
use App\Http\Resources\ActivityLogResource;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use App\Models\User;
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

    public function getComponent(): string
    {
        $role = UserRole::from(request()->user()->getRoleNames()->first());

        $subFolder = match ($role) {
            UserRole::HeadFrontliner => '1',
            UserRole::ITAdmin        => '2',
            UserRole::TeamLeader     => '3',
            UserRole::Consultant     => '4',
            UserRole::HumanResource  => '5',
            default                  => '6',
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
                'recentActivities' => $this->getUserRecentActivities(),
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
        } else {
            return [
                'recentActivities' => $this->getUserRecentActivities(),
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

    // public function getCurrentYearParticipation()
    // {
    //     $user = request()->user();

    //     $x = JobOrder
    // }
}
