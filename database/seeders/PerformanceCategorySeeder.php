<?php

namespace Database\Seeders;

use App\Models\PerformanceCategory;
use Illuminate\Database\Seeder;

class PerformanceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PerformanceCategory::factory(10)->create();
    }
}
