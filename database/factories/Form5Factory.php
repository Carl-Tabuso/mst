<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form5>
 */
class Form5Factory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assigned_person' => Employee::inRandomOrder()->first()->id ?? Employee::factory(),
            'purpose'         => fake()->paragraph(),
        ];
    }
}
