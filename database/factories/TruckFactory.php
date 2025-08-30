<?php

namespace Database\Factories;

use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Truck>
 */
class TruckFactory extends Factory
{
    use RandomEmployee;

    public const MODELS = [
        'Isuzu N-Series',
        'Isuzu F-Series',
        'Mitsubishi Fuso Canter',
        'Hino 300',
        'Hino 500',
        'Hyundai Mighty',
        'Hyundai Xcient',
        'Foton Tornado',
        'JAC Gallop',
        'Tata LPT',
        'Volvo FL',
        'Volvo FMX',
        'MAN TGS',
        'Scania P-Series',
        'Mercedes-Benz Actros',
    ];

    public function definition(): array
    {
        return [
            'model' => fake()->randomElement(self::MODELS),
            'plate_no' => strtoupper(fake()->bothify('???-####')),
            'added_by' => $this->getByPosition('Dispatcher'),
        ];
    }
}
