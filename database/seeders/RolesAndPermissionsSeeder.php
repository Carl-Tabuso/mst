<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = array_map(fn ($case) => [
            'name'       => $case->value,
            'guard_name' => 'web',
        ], UserPermission::cases());

        DB::transaction(function () use ($permissions) {
            Permission::insert($permissions);

            Role::firstOrCreate(['name' => UserRole::Frontliner])
                ->givePermissionTo($this->useValue(UserPermission::getFrontlinerPermissions()));

            Role::firstOrCreate(['name' => UserRole::Dispatcher])
                ->givePermissionTo($this->useValue(UserPermission::getDispatcherPermissions()));

            Role::firstOrCreate(['name' => UserRole::TeamLeader])
                ->givePermissionTo($this->useValue(UserPermission::getTeamLeaderPermissions()));

            Role::firstOrCreate(['name' => UserRole::HeadFrontliner])
                ->givePermissionTo($this->useValue(UserPermission::getHeadFrontlinerPermissions()));
        });
    }

    private function useValue($enum): array
    {
        return array_map(fn ($case) => $case->value, $enum);
    }
}
