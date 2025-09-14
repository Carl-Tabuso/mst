<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): Response
    {
        $isAuthorize = $user->hasAnyRole([
            UserRole::ITAdmin,
            UserRole::HeadFrontliner,
        ]);

        return $isAuthorize
            ? Response::allow()
            : Response::deny();
    }

    public function restore(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::ManageUsers)
            ? Response::allow()
            : Response::deny();
    }

    public function forceDelete(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::ManageUsers)
            ? Response::allow()
            : Response::deny();
    }
}
