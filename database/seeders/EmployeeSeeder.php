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
        $this->seedHeadFrontliners();
        $this->seedHaulers();
        $this->seedItAdmins();
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
            'position_id' => $this->getPositionId('Frontliner'),
            'department_id' => $this->getRandomDepartmentId(),
        ]);
    }
     private function seedHaulers(): void
    {
        Employee::factory(30)->create([
            'position_id' => $this->getPositionId('Hauler'),
        ]);
    }

    private function seedItAdmins(): void
    {
        Employee::factory(5)->create([
            'position_id' => $this->getPositionId('IT Admin'),
        ]);
    }

    private function seedConsultants(): void
    {
        Employee::factory(5)->create([
            'position_id' => $this->getPositionId('Consultant'),
        ]);
    }



    private function seedDispatchers(): void
    {

        Employee::factory(10)->create([
            'position_id' => $this->getPositionId('Dispatcher'),
            'department_id' => $this->getRandomDepartmentId(),

        ]);
    }

    private function seedTeamLeaders(): void
    {
        Employee::factory(10)->create([
            'position_id' => $this->getPositionId('Team Leader'),
            'department_id' => $this->getRandomDepartmentId(),
        ]);
    }

    private function seedHeadFrontliners()
    {
        Employee::factory(5)->create([
            'position_id' => $this->getPositionId('Head Frontliner'),
            'department_id' => $this->getRandomDepartmentId(),
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
