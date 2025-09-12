<?php

namespace App\Services;

use App\Models\User;

class UserService
{
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
