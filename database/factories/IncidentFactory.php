<?php

namespace Database\Factories;

use App\Enums\IncidentStatus;
use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_order_id' => JobOrder::inRandomOrder()->first()->id ?? JobOrder::factory(),
            'occured_at'   => fake()->dateTimeBetween('-1 week', '-1 day'),
            'description'  => fake()->paragraph(),
            'action_taken' => fake()->paragraph(),
            'status'       => fake()->randomElement(IncidentStatus::cases()),
        ];
    }
}
