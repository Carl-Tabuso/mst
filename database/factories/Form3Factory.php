<?php

namespace Database\Factories;

use App\Models\Form4;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form3>
 */
class Form3Factory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form4_id' => Form4::inRandomOrder()->first()->id ?? Form4::factory(),
            'payment_type' => fake()->randomElement(['Cash', 'Online']),
            'appraised_date' => now(),
            'truck_no' => fake()->windowsPlatformToken(),
        ];
    }
}
