<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\JobOrder;
use App\Enums\JobOrderStatus;
use App\Enums\JobOrderServiceType;
use Illuminate\Support\Collection;
use App\Http\Resources\EmployeeResource;

class AnnualReportService
{
    public function processAnnualReport(int $year): Collection
    {
        $data = collect([
            'metrics' => collect(),
            'top'     => collect([
                'client' => '',
                'month'  => '',
                'service' => ''
            ]),
            'frontliners' => [],
            'total' => 0,
            'services' => collect([
                'trends' => collect(),
                'completion' => collect(),
            ]),
        ]);

        $baseQuery = JobOrder::query()
                            ->withTrashed()
                            // ->with('creator')
                            ->whereYear('created_at', $year);

        $clients       = collect();
        $frontlinerIds = collect();
        $serviceTypes  = collect();

        $baseQuery->chunk(100, function (Collection $jobOrders) use ($data, $clients, $frontlinerIds, $serviceTypes) {
            $groupedByMonths = $jobOrders->groupBy(fn ($jobOrder) => $jobOrder->created_at->monthName);

            $groupedByMonths->each(function ($grouped, $month) use ($data) {
                $index = $data['metrics']->search(fn ($metric) => $metric['month'] === $month);

                $completedStatusCount = $this->getCompletedStatusCount($grouped);
                $cancelledStatusCount = $this->getCancelledStatusCount($grouped);

                if ($index !== false) {
                    $metric = $data['metrics']->get($index);

                    $metric['Completed'] += $completedStatusCount;
                    $metric['Cancelled'] += $cancelledStatusCount;

                    $data['metrics']->put($index, $metric);
                } else {
                    $data['metrics']->push([
                        'month'     => $month,
                        'Completed' => $completedStatusCount,
                        'Cancelled' => $cancelledStatusCount,
                    ]);
                }
            });

            $data['top']['month'] = $groupedByMonths->search($groupedByMonths->max());
            $clients->push($jobOrders->pluck('client'));
            $frontlinerIds->push($jobOrders->pluck('created_by'));
            $serviceTypes->push([$jobOrders->pluck('serviceable_type')]);
        });

        // dd($serviceTypes->all());
        $counts    = $clients->flatten()->countBy();
        $maxValue  = $counts->max();
        $topClient = $counts->search($maxValue);

        $data['top']['client'] = $topClient;

        $frontliners = $frontlinerIds->flatten()->countBy()->sortDesc();
        $wrapped = $frontliners->map(fn ($value, $id) => (object) [
            'id' => $id,
            'created' => $value
        ])->values();

        $frontlinerModels = Employee::with('account:avatar')->findMany($frontliners->keys());

        $data['frontliners'] = $wrapped->map(fn ($item, $index) => [
            'employee'              => EmployeeResource::make($frontlinerModels->firstWhere('id', $item->id)),
            'createdJobOrdersCount' => $item->created,
            'rank'                  => $index + 1,
        ])->values();

        $data->put('total', $baseQuery->count());

        $counts    = $serviceTypes->flatten()->countBy();
        $maxValue  = $counts->max();
        $topService = $counts->search($maxValue);
        $data['top']['service'] = JobOrderServiceType::from($topService)->getLabel();
        
        $data['services']['trends'] = $counts->map(fn ($count, $service) => [
            'name' => JobOrderServiceType::from($service)->getLabel(),
            'total' => $count,
        ])->values();

        // dd(
        //     JobOrder::whereYear('created_at', 2024)->count(),
        //     $data,
        //     $serviceTypes,
        //     $counts,
        // );

        return $data;
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