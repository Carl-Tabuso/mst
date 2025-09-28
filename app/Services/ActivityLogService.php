<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Resources\ActivityLogResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

class ActivityLogService
{
    public function getGroupedByDateLogs(?int $perPage = 10): array
    {
        $user            = request()->user();
        $isNotAdminRoles = ! in_array(UserRole::from($user->getRoleNames()->first()), UserRole::adminRoles());

        $paginator = Activity::query()
            ->with(['causer' => ['employee', 'roles']])
            ->when($isNotAdminRoles, function (Builder $query) use ($user) {
                $query->whereHasMorph('causer', [User::class],
                    fn (Builder $subQuery) => $subQuery->where('causer_id', $user->id)
                );
            })
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
