<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class ArchivedEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        activity()->disableLogging();

        Employee::factory()
            ->count(20)
            ->trashed()
            ->create();
    }
}
