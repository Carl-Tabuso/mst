<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobOrderPolicy
{
    public function viewAny(User $user): Response
    {
        return
            $user->hasPermissionTo(UserPermission::ViewAnyJobOrder) ||
            $user->hasRole(UserRole::Frontliner)
                ? Response::allow()
                : Response::deny();
    }

    public function view(User $user, JobOrder $jobOrder): Response
    {
        $isAuthorize = $user->employee->createdJobOrders->contains($jobOrder) ||
            $user->hasPermissionTo(UserPermission::ViewAnyJobOrder);

        return $isAuthorize
            ? Response::allow()
            : Response::deny();
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::CreateJobOrder)
            ? Response::allow()
            : Response::deny();
    }

    public function update(User $user, JobOrder $jobOrder): Response
    {
        return $user->hasPermissionTo(UserPermission::UpdateJobOrder)
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, JobOrder $jobOrder): Response
    {
        return $user->hasPermissionTo(UserPermission::UpdateJobOrder)
            ? Response::allow()
            : Response::deny();
    }

    public function restore(User $user, JobOrder $jobOrder): bool
    {
        return false;
    }

    public function forceDelete(User $user, JobOrder $jobOrder): bool
    {
        return false;
    }
}
