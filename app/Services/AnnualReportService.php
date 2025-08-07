<?php

namespace App\Services;

use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\JobOrder;
use Illuminate\Support\Collection;

class AnnualReportService
{
    private Collection $metrics;

    private Collection $top;

    private Collection $frontliners;

    private $totalJobOrders = 0;

    private Collection $trends;

    private Collection $completion;

    public function __construct()
    {
        $this->metrics = collect();

        $this->top = collect([
            'client'  => '',
            'month'   => '',
            'service' => '',
        ]);

        $this->completion = collect([
            [
                'name'      => JobOrderServiceType::Form4->getLabel(),
                'Completed' => 0,
                'Cancelled' => 0,
            ],
            [
                'name'      => JobOrderServiceType::ITService->getLabel(),
                'Completed' => 0,
                'Cancelled' => 0,
            ],
            [
                'name'      => JobOrderServiceType::Form5->getLabel(),
                'Completed' => 0,
                'Cancelled' => 0,
            ],
        ]);
    }

    public function processAnnualReport(int $year): Collection
    {
        $baseQuery = JobOrder::withTrashed()->whereYear('created_at', $year);

        $clients       = collect();
        $frontlinerIds = collect();
        $serviceTypes  = collect();

        $baseQuery->chunkById(100, function (Collection $jobOrders) use ($clients, $frontlinerIds, $serviceTypes) {
            $groupedByMonths = $jobOrders->groupBy(fn ($jobOrder) => $jobOrder->created_at->monthName);

            $groupedByMonths->each(function ($grouped, $month) {
                $this->setMetrics($grouped, $month);
                $this->calculateJobOrderServiceBreakdown($grouped);
            });

            $this->top['month'] = $groupedByMonths->search($groupedByMonths->max());
            $clients->push($jobOrders->pluck('client'));
            $frontlinerIds->push($jobOrders->pluck('created_by'));
            $serviceTypes->push([
                $jobOrders->pluck('serviceable_type')
                    ->transform(fn ($type) => $type->value),
            ]);
        });

        $this->findTopClient($clients);

        $this->setFrontlinerRankings($frontlinerIds);

        $this->totalJobOrders = $baseQuery->count();

        $this->findTopJobOrderService($serviceTypes);

        $this->setJobOrderServiceTrends($serviceTypes);

        return collect(get_object_vars($this));
    }

    public function getAvailableYears(): Collection
    {
        return JobOrder::query()
            ->withTrashed()
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
    }

    private function setMetrics($grouped, $month): void
    {
        $index = $this->metrics->search(fn ($metric) => $metric['month'] === $month);

        $completedStatusCount = $this->getCompletedStatusCount($grouped);
        $cancelledStatusCount = $this->getCancelledStatusCount($grouped);

        if ($index !== false) {
            $metric = $this->metrics->get($index);

            $metric['Completed'] += $completedStatusCount;
            $metric['Cancelled'] += $cancelledStatusCount;

            $this->metrics->put($index, $metric);
        } else {
            $this->metrics->push([
                'month'     => $month,
                'Completed' => $completedStatusCount,
                'Cancelled' => $cancelledStatusCount,
            ]);
        }
    }

    private function calculateJobOrderServiceBreakdown(Collection $grouped): void
    {
        $grouped->each(function ($jobOrder) {
            $serviceType = $jobOrder->serviceable_type;
            $status      = $jobOrder->status;

            $serviceIndex = $this->completion->search(fn ($item) => $item['name'] === $serviceType->getLabel());

            $completion = $this->completion->get($serviceIndex);

            $completion['Completed'] += (int) ($status === JobOrderStatus::Completed);
            $completion['Cancelled'] += (int) (in_array($status, JobOrderStatus::getCancelledStatuses()));

            $this->completion->put($serviceIndex, $completion);
        });
    }

    private function findTopClient(Collection $clients): void
    {
        $counts    = $clients->flatten()->countBy();
        $maxValue  = $counts->max();
        $topClient = $counts->search($maxValue);

        $this->top['client'] = $topClient;
    }

    private function findTopJobOrderService(Collection $jobOrderServiceTypes): void
    {
        $counts     = $jobOrderServiceTypes->flatten()->countBy();
        $maxValue   = $counts->max();
        $topService = $counts->search($maxValue);

        $this->top['service'] = JobOrderServiceType::from($topService)->getLabel();
    }

    private function setJobOrderServiceTrends(Collection $jobOrderServiceTypes): void
    {
        $counts = $jobOrderServiceTypes->flatten()->countBy();

        $this->trends = $counts->map(fn ($count, $service) => [
            'name'  => JobOrderServiceType::from($service)->getLabel(),
            'total' => $count,
        ])->values();
    }

    private function setFrontlinerRankings(Collection $frontlinerIds): void
    {
        // dd($frontlinerIds);
        $frontliners = $frontlinerIds->flatten()->countBy()->sortDesc();
        $wrapped     = $frontliners->map(fn ($value, $id) => (object) [
            'id'      => $id,
            'created' => $value,
        ])->values();

        $frontlinerModels = Employee::with('account:avatar')->findMany($frontliners->keys());

        $this->frontliners = $wrapped->map(fn ($item, $index) => [
            'employee'              => EmployeeResource::make($frontlinerModels->firstWhere('id', $item->id)),
            'createdJobOrdersCount' => $item->created,
            'rank'                  => $index + 1,
        ])->values();
    }

    private function getCompletedStatusCount(Collection $collection): int
    {
        return $collection->filter(
            fn ($item) => $item->status === JobOrderStatus::Completed
        )->count();
    }

    private function getCancelledStatusCount(Collection $jobOrders): int
    {
        return $jobOrders->filter(
            fn ($jobOrder) => in_array($jobOrder->status, JobOrderStatus::getCancelledStatuses())
        )->count();
    }
}
