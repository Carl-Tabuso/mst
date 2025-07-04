<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Form3Hauling;
use App\Models\Position;
use Illuminate\Database\Seeder;

class Form3HaulingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = now()->subWeek();

        $haulings = Form3Hauling::factory(5)
            ->sequence(fn () => ['date' => $days->addDay()])
            ->create();

        $position = Position::firstWhere(['name' => 'Hauler']);
        $haulers  = Employee::factory(rand(10, 12))->create(['position_id' => $position->id]);

        $haulings->each(fn ($hauling) => $hauling->haulers()->attach($haulers));
    }
}
