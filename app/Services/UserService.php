<?php

namespace App\Services;

use App\Filters\ApplyDateOfArchivalRange;
use App\Filters\FilterOnlyArchived;
use App\Filters\User\FilterRole;
use App\Filters\User\SearchDetails;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Pipeline;

class UserService
{
    public function getAllUsers(?int $perPage = 10, ?string $search = '', ?array $filters = [], bool $archivedOnly = false): mixed
    {
        $archivedAtColumn = new User()->getDeletedAtColumn();

        $pipes = [
            ...($archivedOnly ? [new FilterOnlyArchived] : []),
            new ApplyDateOfArchivalRange($archivedAtColumn, $filters),
            new SearchDetails($search),
            new FilterRole($filters),
        ];

        return Pipeline::send(User::query())
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage, $archivedOnly, $archivedAtColumn) {
                return $query
                    ->latest($archivedOnly ? $archivedAtColumn : null)
                    ->paginate($perPage)
                    ->withQueryString()
                    ->toResourceCollection();
            });
    }

    public function restoreArchivedUser(User $user): bool
    {
        return $user->restore();
    }

    public function bulkRestoreArchivedUsers(array $userIds): mixed
    {
        return User::query()
            ->onlyTrashed()
            ->whereIn('id', $userIds)
            ->restore();
    }

    public function permanentlyDeleteUser(User $user): ?bool
    {
        return $user->forceDelete();
    }
}
