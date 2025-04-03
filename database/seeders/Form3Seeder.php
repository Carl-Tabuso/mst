<?php

namespace Database\Seeders;

use App\Models\Form3;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;

class Form3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $position = Position::firstWhere(['name' => 'Hauler']);

        $haulers = Employee::factory(12)->create(['position_id' => $position->id]);

        Form3::factory()->create()->haulers()->attach($haulers);
    }
}
