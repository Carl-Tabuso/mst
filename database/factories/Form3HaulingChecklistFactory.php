<?php

namespace Database\Factories;

use App\Models\Form3Hauling;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form3HaulingChecklist>
 */
class Form3HaulingChecklistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form3_hauling_id'             => Form3Hauling::factory(),
            'is_vehicle_inspection_filled' => false,
            'is_uniform_ppe_filled'        => false,
            'is_tools_equipment_filled'    => false,
            'is_certify'                   => false,
        ];
    }
}
