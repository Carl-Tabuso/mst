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

        return match ($role) {
            UserRole::HeadFrontliner => 'home/1/Index',
            UserRole::ITAdmin        => 'home/2/Index',
            default                  => 'home/3/Index',
        };
    }

    public function getUserHomeData()
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
        } else {
            return null;
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
                    'total' => $users->whereNull('deleted_at')->count(),
                ],
                [
                    'label' => 'Inactive Users',
                    'total' => $users->whereNotNull('deleted_at')->count(),
                ],
            ]);
    }
}
