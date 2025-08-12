<?php

namespace App\Exports;

use App\Services\AnnualReportService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class FrontlinerRankingsSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private Collection $frontliners) {}

    public function headings(): array
    {
        return [
            'Frontliner',
            'Total Job Orders Created',
        ];
    }

    public function title(): string
    {
        return 'Frontliner Rankings';
    }

    public function collection(): Collection
    {
        $sorted = App::make(AnnualReportService::class)->getFrontlinerRankings($this->frontliners);

        $mapped = $sorted->map(fn ($item) => [
            'frontliner' => $item['employee']->full_name,
            'created'    => $item['createdJobOrdersCount'],
        ]);

        return $mapped;
    }
}
