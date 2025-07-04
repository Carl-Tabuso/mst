<?php

namespace Database\Factories;

use App\Models\Form3Hauling;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form3AssignedPersonnel>
 */
class Form3AssignedPersonnelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form3_hauling_id' => Form3Hauling::factory(),
        ];
    }
}
