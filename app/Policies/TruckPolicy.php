<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TruckPolicy
{
    public function viewAny(User $user): Response
    {
        $isAuthorize = $user->hasAnyRole([
            UserRole::Dispatcher,
            UserRole::HeadFrontliner,
        ]);

        return $isAuthorize
            ? Response::allow()
            : Response::deny();
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::AddNewTruck)
            ? Response::allow()
            : Response::deny();
    }

    public function edit(User $user, Truck $truck): Response
    {
        $hasPermission = $user->hasPermissionTo(UserPermission::UpdateTruck);

        $isCreator = $truck->creator ? $user->employee->is($truck->creator) : true;

        $isAuthorize = $hasPermission && $isCreator;

        return $isAuthorize
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::ArchiveTruck)
            ? Response::allow()
            : Response::deny();
    }

    public function restore(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::RestoreTruck)
            ? Response::allow()
            : Response::deny();
    }

    public function forceDelete(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::ForceDeleteTruck)
            ? Response::allow()
            : Response::deny();
    }
}
