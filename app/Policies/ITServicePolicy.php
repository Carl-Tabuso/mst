<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ITServicePolicy
{
    public function view(User $user, JobOrder $jobOrder): Response
    {
        $isAuthorize = $user->employee->createdJobOrders->contains($jobOrder) ||
            $user->hasPermissionTo(UserPermission::ViewAnyJobOrder);

        return $isAuthorize
            ? Response::allow()
            : Response::deny();
    }
}
