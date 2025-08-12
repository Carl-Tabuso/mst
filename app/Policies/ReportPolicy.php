<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::ViewReportsAnalytics)
            ? Response::allow()
            : Response::deny();
    }
}
