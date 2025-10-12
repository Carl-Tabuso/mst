<?php

namespace App\Services;

use App\Models\Truck;
use Illuminate\Database\Eloquent\Builder;

class TruckService
{
    public function getAllTrucks(?int $perPage = 10, ?string $search = '', ?array $filters = [], bool $archivedOnly = false)
    {
        $data = Truck::query();

        if ($archivedOnly) {
            $data->onlyTrashed();
        }

        $data = $data->with('creator')
            ->when($search, function (Builder $query) use ($search) {
                $query->where(fn (Builder $subQuery) => $subQuery->whereAny([
                    'model',
                    'plate_no',
                ], 'like', "%{$search}%"));
            })
            ->when(isset($filters['dispatchers']) && count($filters['dispatchers']) > 0,
                fn (Builder $query) => $query->whereIn('added_by', $filters['dispatchers'])
            )
            ->latest('updated_at')
            ->paginate($perPage)
            ->withQueryString()
            ->toResourceCollection();

        return $data;
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
