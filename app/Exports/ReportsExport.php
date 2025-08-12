<?php

namespace App\Exports;

use App\Models\JobOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    use Exportable;

    private Collection $frontliners;

    private Collection $clients;

    private Collection $monthly;

    private Collection $serviceComposition;

    public function __construct(private int $year)
    {
        $this->frontliners        = collect();
        $this->clients            = collect();
        $this->monthly            = collect();
        $this->serviceComposition = collect();

        $this->processReportData();
    }

    public function sheets(): array
    {
        return [
            new FrontlinerRankingsSheet($this->frontliners),
            new BusinessClientsStandingSheet($this->clients),
            new MonthlyJobOrderTrendsSheet($this->monthly),
            new JobOrderServiceCompositionBreakdownSheet($this->serviceComposition),
        ];
    }

    private function processReportData(): void
    {
        JobOrder::query()
            ->withTrashed()
            ->whereYear('date_time', $this->year)
            ->chunkById(100, function (Collection $jobOrders) {
                $this->frontliners->push($jobOrders->pluck('created_by'));
                $this->clients->push($jobOrders->pluck('client'));

                $groupedMonths = $jobOrders->groupBy(
                    fn ($jobOrder) => $jobOrder->created_at->monthName
                );
                $this->monthly->push($groupedMonths);

                $this->serviceComposition->push($jobOrders);
            });
    }
}
