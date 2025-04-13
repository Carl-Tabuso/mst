<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form4>
 */
class Form4Factory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_date' => now(),
            'bid_bond' => rand(10000, 50000),
            'or_number' => strtoupper(Str::random()),
        ];
    }

    public function paymentDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_date' => $date,
        ]);
    }
}
