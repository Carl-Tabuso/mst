<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ITService;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InitialOnsiteReportPolicy
{
    public function create(User $user, ITService $iTService): Response
    {
        $isFrontliner = $user->hasRole(UserRole::Frontliner);

        $isJobOrderCreator = $user->employee->createdJobOrders->contains($iTService->jobOrder);

        $canCreateInitialOnsiteReport = $isFrontliner && $isJobOrderCreator;

        return $canCreateInitialOnsiteReport
            ? Response::allow()
            : Response::deny();
    }
}
