<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\EmployeeRating;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeeRatingPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::ViewAnyEmployeeRating) ||
        $user->hasAnyRole([UserRole::TeamLeader, UserRole::Regular, UserRole::Consultant])
        ? Response::allow()
        : Response::deny();
    }

    public function view(User $user, EmployeeRating $employeeRating): Response
    {
        $isAuthorized = $user->employee->id === $employeeRating->employee_id ||
        $user->hasPermissionTo(UserPermission::ViewAnyEmployeeRating);

        return $isAuthorized
        ? Response::allow()
        : Response::deny();
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::CreateEmployeeRating)
        ? Response::allow()
        : Response::deny();
    }

    public function update(User $user, EmployeeRating $employeeRating): Response
    {
        $isCreatedByUser = $user->employee->id === $employeeRating->rater_id;

        $isAuthorized = $isCreatedByUser &&
        $user->hasPermissionTo(UserPermission::UpdateEmployeeRating);

        return $isAuthorized
        ? Response::allow()
        : Response::deny();
    }

    public function delete(User $user, ?EmployeeRating $employeeRating = null): Response
    {
        $isCreatedByUser = $employeeRating
        ? $user->employee->id === $employeeRating->rater_id
        : true;

        $isAuthorized = $isCreatedByUser &&
        $user->hasPermissionTo(UserPermission::DeleteEmployeeRating);

        return $isAuthorized
        ? Response::allow()
        : Response::deny();
    }

    public function export(User $user): Response
    {
        return $user->hasPermissionTo(UserPermission::ExportEmployeeRating)
        ? Response::allow()
        : Response::deny();
    }

    public function restore(User $user, EmployeeRating $employeeRating): bool
    {
        return false;
    }

    public function forceDelete(User $user, EmployeeRating $employeeRating): bool
    {
        return false;
    }
}
