<?php

namespace Database\Factories;

use App\Models\Form4;
use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form3>
 */
class Form3Factory extends Factory
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
            'form4_id'       => Form4::inRandomOrder()->first()->id ?? Form4::factory(),
            'payment_type'   => fake()->randomElement(['Cash', 'Online']),
            'appraised_date' => now(),
            'truck_no'       => fake()->windowsPlatformToken(),
            'from'           => $from = fake()->optional()->dateTimeBetween('-1 week'),
            'to'             => $from ? now() : null,
            'team_leader'    => $this->getByPosition('Team Leader')->id,
            'team_driver'    => $this->getByPosition('Driver')->id,
            'safety_officer' => $this->getByPosition('Safety Officer')->id,
            'team_mechanic'  => $this->getByPosition('Mechanic')->id,
        ];
    }
}
