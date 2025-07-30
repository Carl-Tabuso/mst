<?php

namespace Database\Factories;

use App\Enums\HaulingStatus;
use App\Models\Form3;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form3Hauling>
 */
class Form3HaulingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form3_id' => Form3::factory(),
            'truck_no' => strtoupper(fake()->bothify('???-####')),
            'status'   => fake()->randomElement(HaulingStatus::cases()),
        ];
    }

    public function status(HaulingStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status->value,
        ]);
    }
}
