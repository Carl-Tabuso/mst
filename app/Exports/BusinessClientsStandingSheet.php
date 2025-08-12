<?php

namespace App\Exports;

use App\Services\AnnualReportService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class BusinessClientsStandingSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private Collection $clients) {}

    public function headings(): array
    {
        return [
            'Business Client',
            'Total Availed Services',
        ];
    }

    public function title(): string
    {
        return 'Business Clients Standing';
    }

    public function collection(): Collection
    {
        $sorted = App::make(AnnualReportService::class)->getClientRankings($this->clients);

        $mapped = $sorted->map(fn ($total, $client) => [
            $client, $total,
        ]);

        return $mapped;
    }
}
