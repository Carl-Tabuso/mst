<?php

namespace Database\Factories;

use App\Models\ITService;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class ITServiceTechnicianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'it_service_id' => ITService::factory(),
            'technicians'   => Employee::factory(), // If your pivot column is 'technicians'
        ];
    }
}
