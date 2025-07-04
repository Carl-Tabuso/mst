<?php

namespace Database\Seeders;

use App\Models\PerformanceRating;
use Illuminate\Database\Seeder;

class PerformanceRatingSeeder extends Seeder
{
    const RATINGS = [
        1 => 'Needs Improvement',
        2 => 'Meets Expectations',
        3 => 'Exceeds Expectations',
        4 => 'Outstanding',
        5 => 'Exceptional',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::RATINGS as $scale => $name) {
            PerformanceRating::create([
                'scale' => $scale,
                'name'  => $name,
            ]);
        }
    }
}
