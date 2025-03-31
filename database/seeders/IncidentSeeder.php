<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Incident;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Incident::factory(3)->create()->each(function ($incident) {
            $incident->involvedEmployees()->attach(Employee::inRandomOrder()->take(rand(2, 3))->get());
            $incident->injuredEmployees()->attach(Employee::inRandomOrder()->take(rand(2, 3))->get());
        });
    }
}
