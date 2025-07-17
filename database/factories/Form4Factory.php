<?php

namespace Database\Factories;

use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form4>
 */
class Form4Factory extends Factory
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
            'payment_date' => now(),
            'bid_bond'     => rand(10000, 50000),
            'or_number'    => strtoupper(Str::random()),
            'dispatcher'   => $this->getByPosition('Dispatcher')
        ];
    }

    public function paymentDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_date' => $date,
        ]);
    }
}
