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
        $isAuthorized = $user->employee->createdJobOrders->contains($jobOrder) &&
            $user->hasPermissionTo(UserPermission::UpdateJobOrder);

        return $isAuthorized
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, ?JobOrder $jobOrder = null): Response
    {
        $isCreatedByUser = $jobOrder
            ? $user->employee->createdJobOrders->contains($jobOrder)
            : true;

        $isAuthorized = $isCreatedByUser &&
            $user->hasPermissionTo(UserPermission::UpdateJobOrder);

        return $isAuthorized
            ? Response::allow()
            : Response::deny();
    }

    public function deleteBatch(User $user, array $jobOrders): bool
    {
        return false;
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
