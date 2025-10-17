<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Filters\ApplyDateOfArchivalRange;
use App\Filters\FilterOnlyArchived;
use App\Filters\Truck\FilterByCreators;
use App\Filters\Truck\SearchDetails;
use App\Models\Employee;
use App\Models\Truck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Pipeline;

class TruckService
{
    public function getAllTrucks(?int $perPage = 10, ?string $search = '', ?array $filters = [], bool $archivedOnly = false): ResourceCollection
    {
        $model = new Truck;

        $baseQuery = $model::query();
        $baseQuery->with('creator');

        $pipes = [
            new FilterByCreators($filters),
            new SearchDetails($search),
        ];

        if ($archivedOnly) {
            array_unshift($pipes,
                new FilterOnlyArchived,
                new ApplyDateOfArchivalRange($model->getDeletedAtColumn(), $filters),
            );
        }

        return Pipeline::send($baseQuery)
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage, $archivedOnly, $model) {
                $orderByColumn = $archivedOnly
                    ? $model->getDeletedAtColumn()
                    : $model->getUpdatedAtColumn();

                return $query
                    ->latest($orderByColumn)
                    ->paginate($perPage)
                    ->withQueryString()
                    ->toResourceCollection();
            });
    }

    public function getDispatchers(): ResourceCollection
    {
        return Employee::query()
            ->with('account.roles')
            ->whereHas('account', fn (Builder $query) => $query->role(UserRole::Dispatcher))
            ->get()
            ->toResourceCollection();
    }

    public function archiveTruck(Truck $truck): ?bool
    {
        return $truck->delete();
    }

    public function restoreArchivedTruck(Truck $truck): bool
    {
        return $truck->restore();
    }

    public function bulkRestoreArchivedTrucks(array $truckIds): mixed
    {
        return Truck::query()
            ->onlyTrashed()
            ->whereIn('id', $truckIds)
            ->restore();
    }

    public function permanentlyDeleteTruck(Truck $truck): ?bool
    {
        return $truck->forceDelete();
    }
}
