<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechnicianSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $employees = Employee::factory(5)->create([
                'position_id' => Position::firstWhere(['name' => 'Technician'])->id,
            ]);

            $employees->each(function (Employee $employee) {
                $user = User::factory()->create([
                    'employee_id' => $employee->id,
                ]);

                $user->assignRole(UserRole::Regular);
            });
        });
    }
}
