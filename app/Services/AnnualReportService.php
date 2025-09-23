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
        $baseQuery = JobOrder::withTrashed()->whereYear('date_time', $year);

        $clients       = collect();
        $frontlinerIds = collect();
        $serviceTypes  = collect();
        $monthlyItems  = collect();

        $baseQuery->chunkById(100, function (Collection $jobOrders) use ($clients, $frontlinerIds, $serviceTypes, $monthlyItems) {
            $groupedMonths = $jobOrders->groupBy(
                fn ($jobOrder) => $jobOrder->created_at->monthName
            );

            $groupedMonths->each(function ($grouped, $month) {
                $this->setMetrics($grouped, $month);
                $this->calculateJobOrderServiceBreakdown($grouped);
            });

            $clients->push($jobOrders->pluck('client'));

            $frontlinerIds->push($jobOrders->pluck('created_by'));

            $serviceTypes->push([
                $jobOrders->pluck('serviceable_type')
                    ->transform(fn ($type) => $type->value),
            ]);

            $monthlyItems->push($groupedMonths);
        });

        $this->top['client'] = $this->getTopClient($clients);

        $this->top['month'] = $this->getTopMonth($monthlyItems);

        $this->frontliners = $this->getFrontlinerRankings($frontlinerIds);

        $this->totalJobOrders = $baseQuery->count();

        $this->findTopJobOrderService($serviceTypes);

        $this->trends = $this->getJobOrderServiceTrends($serviceTypes);

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

            $serviceIndex = $this->completion->search(
                fn ($item) => $item['name'] === $serviceType->getLabel()
            );

            $completion = $this->completion->get($serviceIndex);

            $completion['Completed'] += (int) ($status === JobOrderStatus::Completed);
            $completion['Cancelled'] += (int) (in_array($status, JobOrderStatus::getCancelledStatuses()));

            $this->completion->put($serviceIndex, $completion);
        });
    }

    private function getTopClient(Collection $clients): string
    {
        $clientRankings = $this->getClientRankings($clients);

        $topClient = $clientRankings->keys()->first();

        return $topClient;
    }

    public function getClientRankings(Collection $clients): Collection
    {
        $counts    = $clients->flatten()->countBy();
        $sorted    = $counts->sortDesc();

        return $sorted;
    }

    private function getTopMonth(Collection $monthlyItems): string
    {
        $monthRankings = $this->getMonthRankings($monthlyItems);

        $topMonth = $monthRankings->keys()->first();

        return $topMonth;
    }

    public function getMonthRankings(Collection $monthlyItems)
    {
        $index = $monthlyItems->reduce(function (Collection $carry, Collection $items) {
            $items->each(function (Collection $subItems, string $month) use ($carry) {
                $carry[$month] = ($carry[$month] ?? 0) + $subItems->count();
            });

            return $carry;
        }, collect());

        return $index->sortDesc();
    }

    private function findTopJobOrderService(Collection $jobOrderServiceTypes): void
    {
        $counts     = $jobOrderServiceTypes->flatten()->countBy();
        $maxValue   = $counts->max();
        $topService = $counts->search($maxValue);

        $this->top['service'] = JobOrderServiceType::from($topService)->getLabel();
    }

    public function getJobOrderServiceTrends(Collection $jobOrderServiceTypes): Collection
    {
        $counts = $jobOrderServiceTypes->flatten()->countBy();

        $sorted = $counts->map(fn ($count, $service) => [
            'name'  => JobOrderServiceType::from($service)->getLabel(),
            'total' => $count,
        ])->values();

        return $sorted;
    }

    public function getFrontlinerRankings(Collection $frontlinerIds): Collection
    {
        $frontliners = $frontlinerIds->flatten()->countBy()->sortDesc();
        $wrapped     = $frontliners->map(fn ($value, $id) => (object) [
            'id'      => $id,
            'created' => $value,
        ])->values();

        $frontlinerModels = Employee::with('account')->findMany($frontliners->keys());

        $rankings = $wrapped->map(fn ($item, $index) => [
            'employee'              => EmployeeResource::make($frontlinerModels->firstWhere('id', $item->id)),
            'createdJobOrdersCount' => $item->created,
            'rank'                  => $index + 1,
        ])->values();

        return $rankings;
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
