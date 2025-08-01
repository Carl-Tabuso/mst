<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Facades\Agent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Spatie\Activitylog\Models\Activity;

class ActivityLogExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Timestamp',
            'Description',
            'IP Address',
            'Browser',
            'Platform',
            'Employee User',
        ];
    }

    public function collection(): Collection
    {
        $mappedLogs = collect();

        Activity::query()
            ->latest()
            ->with(['causer.employee'])
            ->chunk(100, fn (EloquentCollection $activities) => $mappedLogs->push($activities->map(fn (Activity $activity) => [
                $activity->created_at,
                $activity->description,
                $activity->properties['ip_address'],
                Agent::browser($activity->properties['user_agent']),
                Agent::platform($activity->properties['user_agent']),
                $activity->causer->employee->full_name ?? 'System Generated',
            ]))
            );

        return $mappedLogs;
    }
}
