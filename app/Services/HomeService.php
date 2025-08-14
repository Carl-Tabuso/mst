<?php

namespace App\Services;

use App\Enums\JobOrderServiceType;
use App\Http\Resources\ActivityLogResource;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use Illuminate\Support\Collection;

class HomeService
{
    public function __construct(
        private JobOrderService $jobOrderService,
        private ActivityLogService $activityLogService,
    ) {}

    public function getEmployeeStatusCounts(): array
    {
        return Employee::query()
            ->latest()
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
        $activities =  $this->activityLogService->getRecentLogs(5);

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
                'ticket' => $jobOrder->ticket,
                'serviceType' => $jobOrder->serviceable_type,
                'status' => $jobOrder->status,
                'humanDiff' => $jobOrder->date_time->diffForHumans(),
                'frontliner' => $jobOrder->creator->full_name
            ]);
    }

    public function recentJobOrderCorrectionRequests()
    {
        $x = JobOrderCorrection::query()
            ->get();
    }
}
