<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Position;
use App\Traits\RandomEmployee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    use RandomEmployee;

    public function run(): void
    {
        $this->seedFrontliners();
        $this->seedDispatchers();
        $this->seedTeamLeaders();
        $this->seedHeadFrontliner();
        $this->seedHaulers();
        $this->seedItAdmins();
        $this->seedConsultants();
        $this->seedHumanResources();
    }

    private function seedFrontliners(): void
    {
        Employee::factory(10)->create([
            'position_id' => $this->getPositionId('Frontliner'),
        ]);
    }

    private function seedDispatchers(): void
    {
        Employee::factory(10)->create([
            'position_id' => $this->getPositionId('Dispatcher'),
        ]);
    }

    private function seedTeamLeaders(): void
    {
        Employee::factory(10)->create([
            'position_id' => $this->getPositionId('Team Leader'),
        ]);
    }

    private function seedHeadFrontliner(): void
    {
        Employee::factory()->create([
            'position_id' => $this->getPositionId('Head Frontliner'),
        ]);
    }

    private function seedHaulers(): void
    {
        Employee::factory(15)->create([
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
}
