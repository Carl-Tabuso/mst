<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Traits\RandomEmployee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    use RandomEmployee;

    public function run(): void
    {
        $this->seedDepartments();
        $this->seedFrontliners();
        $this->seedDispatchers();
        $this->seedTeamLeaders();
        $this->seedHaulers();
        $this->seedConsultants();
        $this->seedHumanResources();
    }

    private function seedDepartments()
    {
        $departments = [
            ['name' => 'Operations', 'code' => 'OPS'],
            ['name' => 'Logistics', 'code' => 'LOG'],
            ['name' => 'Human Resources', 'code' => 'HR'],
            ['name' => 'Field Services', 'code' => 'FLD'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }

    private function seedFrontliners()
    {
        Employee::factory(10)->create([
            'position_id' => $this->getPositionId('Team Leader'),
        ]);
    }

    private function seedHaulers(): void
    {
        Employee::factory(15)->create([
            'position_id' => $this->getPositionId('Hauler'),
        ]);
    }

    private function seedConsultants(): void
    {
        Employee::factory(5)->create([
            'position_id' => $this->getPositionId('Consultant'),
        ]);
    }

    private function seedHumanResources(): void
    {
        Employee::factory(3)->create([
            'position_id' => $this->getPositionId('Human Resource'),
        ]);
    }

    private function getPositionId($positionName): int
    {
        return Position::firstWhere(['name' => $positionName])->id;
    }

    private function getRandomDepartmentId(): int
    {
        return Department::inRandomOrder()->first()->id;
    }
}
