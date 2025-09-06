<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\ITService;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FinalOnsiteReportPolicy
{
    public function create(User $user, ITService $iTService): Response
    {
        $isFrontliner = $user->hasPermissionTo(UserPermission::UpdateJobOrder);

        $isJobOrderCreator = $user->employee->createdJobOrders->contains($iTService->jobOrder);

        $canCreateFinalOnsiteReport = $isFrontliner && $isJobOrderCreator;

        return $canCreateFinalOnsiteReport
             ? Response::allow()
             : Response::deny();
    }
}
