<?php

namespace App\Exports;

use App\Models\JobOrder;
use App\Services\AnnualReportService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\FromCollection;

class FrontlinerRankingsExport implements FromCollection
{
    public function __construct(private int $year) {}

    public function collection(): Collection
    {
        $frontlinerIds = collect();

        JobOrder::query()
            ->withTrashed()
            ->whereYear('date_time', $this->year)
            ->chunkById(100, function (Collection $jobOrders) use ($frontlinerIds) {
                $frontlinerIds->push($jobOrders->pluck('created_by'));
            });

        $sorted = App::make(AnnualReportService::class)->getFrontlinerRankings($frontlinerIds);

        $mapped = $sorted->map(fn ($item) => [
            ...$item,
            'employee' => $item['employee']->full_name
        ]);

        return $mapped;
    }
}
