<?php

namespace App\Exports;

use App\Enums\JobOrderServiceType;
use App\Models\JobOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JobOrderExport implements FromCollection, WithHeadings
{
    public function __construct(protected array $jobOrderIds) {}

    public function headings(): array
    {
        return [
            'Job Order',
            'Client',
            'Service Type',
            'Date & Time of Service',
            'Address',
            'Department',
            'Contact Person',
            'Contact Number',
            'Frontliner',
            'Date Logged/Created',
            'Date Archived',
        ];
    }

    public function collection(): Collection
    {
        return
            JobOrder::query()
                ->withTrashed()
                ->whereIn('id', $this->jobOrderIds)
                ->with('creator')
                ->get()
                ->map(fn (JobOrder $jobOrder) => [
                    $jobOrder->ticket,
                    $jobOrder->client,
                    JobOrderServiceType::from($jobOrder->serviceable->getMorphClass())->getLabel(),
                    $jobOrder->date_time,
                    $jobOrder->address,
                    $jobOrder->department,
                    $jobOrder->contact_person,
                    $jobOrder->contact_no,
                    $jobOrder->creator->full_name,
                    $jobOrder->created_at,
                    $jobOrder->archived_at,
                ]);
    }
}
