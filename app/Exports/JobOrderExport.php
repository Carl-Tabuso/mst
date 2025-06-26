<?php

namespace App\Exports;

use App\Enums\JobOrderServiceType;
use App\Models\JobOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JobOrderExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Client',
            'Service Type',
            'Date & Time of Service',
            'Address',
            'Department',
            'Contact Person',
            'Contact Number',
            'Frontliner',
            'Date Logged/Created',
        ];
    }

    public function collection(): Collection
    {
        return
            JobOrder::with('creator')
                ->get()
                ->map(fn ($jO) => [
                    $jO->client,
                    JobOrderServiceType::from($jO->serviceable->getMorphClass())->getLabel(),
                    $jO->date_time,
                    $jO->address,
                    $jO->department,
                    $jO->contact_person,
                    $jO->contact_no,
                    $jO->creator->full_name,
                    $jO->created_at,
                ]);
    }
}
