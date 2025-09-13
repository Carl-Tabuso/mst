<?php

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Create the new employee rating permissions
        $permissions = [
            UserPermission::ViewAnyEmployeeRating->value,
            UserPermission::CreateEmployeeRating->value,
            UserPermission::UpdateEmployeeRating->value,
            UserPermission::DeleteEmployeeRating->value,
            UserPermission::ExportEmployeeRating->value,
        ];

        foreach ($permissions as $permissionName) {
            Permission::create([
                'name'       => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // Assign permissions to existing roles
        $teamLeaderRole = Role::where('name', UserRole::TeamLeader->value)->first();
        if ($teamLeaderRole) {
            $teamLeaderRole->givePermissionTo([
                UserPermission::ViewAnyEmployeeRating->value,
                UserPermission::CreateEmployeeRating->value,
                UserPermission::UpdateEmployeeRating->value,
            ]);
        }

        $consultantRole = Role::where('name', UserRole::Consultant->value)->first();
        if ($consultantRole) {
            $consultantRole->givePermissionTo([
                UserPermission::ViewAnyEmployeeRating->value,
                UserPermission::ExportEmployeeRating->value,
            ]);
        }

        $regularRole = Role::where('name', UserRole::Regular->value)->first();
        if ($regularRole) {
            $regularRole->givePermissionTo([
                UserPermission::ViewAnyEmployeeRating->value,
                UserPermission::CreateEmployeeRating->value,
            ]);
        }
    }

    public function down(): void
    {
        $permissions = [
            UserPermission::ViewAnyEmployeeRating->value,
            UserPermission::CreateEmployeeRating->value,
            UserPermission::UpdateEmployeeRating->value,
            UserPermission::DeleteEmployeeRating->value,
            UserPermission::ExportEmployeeRating->value,
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
