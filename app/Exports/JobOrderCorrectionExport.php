<?php

namespace App\Exports;

use App\Models\JobOrderCorrection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JobOrderCorrectionExport implements FromCollection, WithHeadings
{
    public function __construct(private array $correctionIds) {}

    public function headings(): array
    {
        return [
            'Ticket',
            'Reason',
            'Requesting Frontliner',
            'Status',
            'Errors',
            'Date Requested',
            'Old Information',
            'New Information',
        ];
    }

    public function collection(): Collection
    {
        return JobOrderCorrection::query()
            ->withTrashed()
            ->with('jobOrder.creator')
            ->whereIn('id', $this->correctionIds)
            ->get()
            ->map(fn (JobOrderCorrection $correction) => [
                $correction->jobOrder->ticket,
                $correction->reason,
                $correction->jobOrder->creator->full_name,
                $correction->status->value,
                $correction->jobOrder->error_count,
                $correction->created_at,
                $correction->properties['before'],
                $correction->properties['after'],
            ]);
    }
}
