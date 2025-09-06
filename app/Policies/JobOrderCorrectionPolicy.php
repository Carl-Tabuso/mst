<?php

namespace App\Policies;

use App\Enums\JobOrderCorrectionRequestStatus;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobOrderCorrectionPolicy
{
    public function __construct() {}

    public function viewAny(User $user): Response
    {
        return
            $user->hasRole(UserRole::Frontliner) ||
            $user->hasPermissionTo(UserPermission::ViewAnyJobOrderCorrection)
                ? Response::allow()
                : Response::deny();
    }

    public function view(User $user, JobOrderCorrection $correction): Response
    {
        $isAuthorize = $user->employee->createdJobOrders->contains($correction->jobOrder) ||
            $user->hasPermissionTo(UserPermission::ViewAnyJobOrderCorrection);

        return $isAuthorize
            ? Response::allow()
            : Response::deny();
    }

    public function create(User $user, JobOrder $jobOrder): Response
    {
        $hasPermission     = $user->hasPermissionTo(UserPermission::CreateJobOrderCorrection);
        $isJobOrderCreator = $user->employee->createdJobOrders->contains($jobOrder);

        $canCreateJobOrderCorrection = $hasPermission && $isJobOrderCreator;

        return $canCreateJobOrderCorrection
            ? Response::allow()
            : Response::deny();
    }

    public function update(User $user, JobOrderCorrection $correction): Response
    {
        $canUpdate = $correction->status === JobOrderCorrectionRequestStatus::Pending &&
            $user->hasPermissionTo(UserPermission::UpdateJobOrderCorrection);

        return $canUpdate
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::UpdateJobOrderCorrection)
            ? Response::allow()
            : Response::deny();
    }
}
