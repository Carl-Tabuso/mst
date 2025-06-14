<?php

namespace Database\Seeders;

use App\Models\Form3Hauling;
use App\Traits\RandomEmployee;
use Illuminate\Database\Seeder;

class Form3AssignedPersonnelSeeder extends Seeder
{
    use RandomEmployee;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Form3Hauling::all()
            ->each(function ($hauling) {
                $hauling->assignedPersonnel()->create([
                    'team_leader'    => $this->getByPosition('Team Leader')->id,
                    'team_driver'    => $this->getByPosition('Driver')->id,
                    'safety_officer' => $this->getByPosition('Safety Officer')->id,
                    'team_mechanic'  => $this->getByPosition('Mechanic')->id,
                ]);
            });
    }
}
