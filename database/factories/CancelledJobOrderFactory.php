<?php

namespace Database\Factories;

use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CancelledJobOrder>
 */
class CancelledJobOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reason' => fake()->paragraph(),
        ];
    }
}
