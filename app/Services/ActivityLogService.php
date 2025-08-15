<?php

namespace App\Services;

use App\Http\Resources\ActivityLogResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

class ActivityLogService
{
    public function getGroupedByDateLogs(?int $perPage = 10): array
    {
        $paginator = Activity::query()
            ->with(['causer' => ['employee', 'roles']])
            ->latest()
            ->paginate($perPage);

        $activities = $paginator->setCollection(
            collect($paginator->items())
                ->groupBy(fn (Activity $activity) => $activity->created_at->toDateString())
                ->map(fn (Collection $grouped, string $date) => [
                    'date'  => $date,
                    'items' => ActivityLogResource::collection($grouped),
                ])
                ->values()
        )->toArray();

        return $activities;
    }
}
