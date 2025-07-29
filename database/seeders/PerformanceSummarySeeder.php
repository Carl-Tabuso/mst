<?php

namespace Database\Seeders;

use App\Models\PerformanceSummary;
use Illuminate\Database\Seeder;

class PerformanceSummarySeeder extends Seeder
{
    public function run(): void
    {
        PerformanceSummary::factory(50)->create();
    }
}
