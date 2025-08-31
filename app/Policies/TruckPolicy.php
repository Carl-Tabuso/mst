<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TruckPolicy
{
    public function __construct()
    {
        //
    }

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
        return $user->hasRole(UserRole::Dispatcher)
            ? Response::allow()
            : Response::deny();
    }

    public function edit(User $user): Response
    {
        return $user->hasRole(UserRole::Dispatcher)
            ? Response::allow()
            : Response::deny();
    }
}
