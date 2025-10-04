<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;

class HumanResourceSeeder extends Seeder
{
    public function run(): void
    {
        Employee::factory()
            ->create([
                'email'       => config('auth.initial_hr_email'),
                'position_id' => Position::firstWhere(['name' => 'Human Resource'])->id,
            ]);
    }
}
