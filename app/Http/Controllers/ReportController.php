<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $data = collect([
            'metrics' => collect(),
            'top'     => collect([
                'client' => null,
                'month'  => '',
            ]),
            'frontliners' => [],
        ]);

        // score  = total job orders created - total corrections made

        $clients       = collect();
        $frontlinerIds = collect();

        JobOrder::query()
            ->withTrashed()
            // ->with('creator')
            ->whereYear('created_at', $request->integer('year', 2024))
            ->chunk(100, function (Collection $jobOrders) use ($data, $clients, $frontlinerIds) {
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
            });

        $counts    = $clients->flatten()->countBy();
        $maxValue  = $counts->max();
        $topClient = $counts->search($maxValue);

        $data['top']['client'] = $topClient;

        $frontliners = $frontlinerIds->flatten()->countBy()->sortDesc();

        $frontlinerModels = Employee::with('account:avatar')->findMany($frontliners->keys());

        $data['frontliners'] = $frontliners->map(fn ($created, $frontliner) => [
            'employee'              => EmployeeResource::make($frontlinerModels->firstWhere('id', $frontliner)),
            'createdJobOrdersCount' => $created,
            'rank'                  => $frontliners->values()->search($created) + 1,
        ])->values();

        // dd(
        //     JobOrder::whereYear('created_at', 2024)->count(),
        //     $data,
        // );

        return Inertia::render('reports/Index', compact('data'));
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
