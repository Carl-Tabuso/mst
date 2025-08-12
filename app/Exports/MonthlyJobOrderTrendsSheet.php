<?php

namespace App\Exports;

use App\Services\AnnualReportService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class MonthlyJobOrderTrendsSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private Collection $monthlyItems) {}

    public function headings(): array
    {
        return [
            'Month',
            'Total Job Orders',
        ];
    }

    public function title(): string
    {
        return 'Monthly Job Order Trends';
    }

    public function collection(): Collection
    {
        $sorted = App::make(AnnualReportService::class)->getMonthRankings($this->monthlyItems);

        $mapped = $sorted->map(fn ($total, $month) => [
            $month,
            $total,
        ])->values();

        return $mapped;
    }
}
