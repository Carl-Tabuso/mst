<?php

namespace Database\Factories;

use App\Enums\IncidentStatus;
use App\Models\JobOrder;
use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    use RandomEmployee;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'occured_at'      => fake()->dateTimeBetween('-1 week', '-1 day'),
            'description'     => fake()->paragraph(),
            'subject'         => fake()->sentence(),
            'location'        => fake()->city(),
            'infraction_type' => fake()->word(),
            'status'          => fake()->randomElement(IncidentStatus::cases()),
            'created_by'      => $this->getByPosition('Team Leader'),

        ];
    }
}
