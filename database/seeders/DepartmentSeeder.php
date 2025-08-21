<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Operations', 'code' => 'OPS'],
            ['name' => 'Logistics', 'code' => 'LOG'],
            ['name' => 'Human Resources', 'code' => 'HR'],
            ['name' => 'Field Services', 'code' => 'FLD'],
            ['name' => 'Administration', 'code' => 'ADMIN'],
            ['name' => 'Technical Services', 'code' => 'TECH'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }
}
