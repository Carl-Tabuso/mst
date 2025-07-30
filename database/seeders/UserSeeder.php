<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        activity()->disableLogging();

        Employee::each(function ($employee) {
            if (! $employee->account) {
                User::factory(['employee_id' => $employee->id])->create();

                $employee->refresh();
            }

            $role = UserRole::tryFrom(strtolower($employee->position->name));

            $role
                ? $employee->account->assignRole($role)
                : $employee->account->assignRole(UserRole::Regular);
        });
    }
}
