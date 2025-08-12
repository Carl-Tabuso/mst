<?php

namespace App\Exports;

use App\Enums\JobOrderStatus;
use App\Models\JobOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class JobOrderServiceCompositionBreakdownSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private Collection $jobOrders) {}

    public function headings(): array
    {
        return [
            'Service Type',
            'Completed',
            'Cancelled',
            'Total',
        ];
    }

    public function title(): string
    {
        return 'Job Order Service Composition Breakdown';
    }

    public function collection(): Collection
    {
        return $this->jobOrders
            ->flatten()
            ->groupBy(fn (JobOrder $jobOrder) => $jobOrder->serviceable_type->getLabel())
            ->map(function (Collection $jobOrders, string $serviceType) {
                $completed = $jobOrders->where('status', JobOrderStatus::Completed)->count();
                $cancelled = $jobOrders->whereIn('status', JobOrderStatus::getCancelledStatuses())->count();

                return [
                    $serviceType,
                    $completed,
                    $cancelled,
                    $completed + $cancelled,
                ];
            })->values();
    }
}
