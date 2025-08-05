<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Position;
use App\Traits\RandomEmployee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    use RandomEmployee;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedFrontliners();
        $this->seedDispatchers();
        $this->seedTeamLeaders();
        $this->seedHeadFrontliners();
        $this->seedHaulers();
    }

    private function seedFrontliners()
    {
        Employee::factory(10)->create([
            'position_id' => $this->getPositionId('Frontliner'),
        ]);
    }

    private function seedDispatchers()
    {
        Employee::factory(10)->create([
            'position_id' => $this->getPositionId('Dispatcher'),
        ]);
    }

    private function seedTeamLeaders()
    {
        Employee::factory(10)->create([
            'position_id' => $this->getPositionId('Team Leader'),
        ]);
    }

    private function seedHeadFrontliners()
    {
        Employee::factory(5)->create([
            'position_id' => $this->getPositionId('Head Frontliner'),
        ]);
    }

    private function seedHaulers()
    {
        Employee::factory(30)->create([
            'position_id' => $this->getPositionId('Hauler'),
        ]);
    }

    private function getPositionId($positionName): int
    {
        return Position::firstWhere(['name' => $positionName])->id;
    }
}
