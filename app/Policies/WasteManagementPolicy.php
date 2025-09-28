<?php

namespace App\Policies;

use App\Enums\JobOrderStatus;
use App\Enums\UserPermission;
use App\Models\Form4;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class WasteManagementPolicy
{
    public function updateAppraisalInformation(User $user, Form4 $form4): Response
    {
        $isAuthorize = $user->employee->is($form4->dispatcher);

        return $isAuthorize
            ? Response::allow()
            : Response::deny();
    }

    public function storeAppraisalInformation(User $user, Form4 $form4): Response
    {
        $forAppraisal        = $form4->jobOrder->status === JobOrderStatus::ForAppraisal;
        $canAssignAppraisers = $user->hasPermissionTo(UserPermission::AssignAppraisers);

        $isAuthorize = $forAppraisal && $canAssignAppraisers;

        return $isAuthorize
            ? Response::allow()
            : Response::deny();
    }
}
