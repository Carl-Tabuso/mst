<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    public function viewAny(User $user): Response
    {
        $isAuthorize = $user->hasAnyRole([
            UserRole::HumanResource,
            UserRole::HeadFrontliner,
        ]);

        return $isAuthorize
            ? Response::allow()
            : Response::deny();
    }

    public function restore(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::ManageEmployees)
            ? Response::allow()
            : Response::deny();
    }

    public function forceDelete(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::ManageEmployees)
            ? Response::allow()
            : Response::deny();
    }
}
