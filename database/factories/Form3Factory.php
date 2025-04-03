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
            'form4_id' => Form4::inRandomOrder()->first()->id ?? Form4::factory(),
            'team_leader' => $this->getByPosition('Team Leader'),
            'team_driver' => $this->getByPosition('Driver'),
            'safety_officer' => $this->getByPosition('Safety Officer'),
            'team_mechanic' => $this->getByPosition('Mechanic'),
            'truck_no' => fake()->windowsPlatformToken(),
        ];
    }
}
