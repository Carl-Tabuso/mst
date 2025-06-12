<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name'  => fake()->firstName(),
            'middle_name' => fake()->randomElement([fake()->firstName(), null]),
            'last_name'   => fake()->lastName(),
            'suffix'      => fake()->randomElement(['Sr.', 'Jr.', 'II', 'III', null]),
            'position_id' => Position::inRandomOrder()->first()->id,
        ];
    }
}
