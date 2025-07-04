<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PositionSeeder::class,
            UserSeeder::class,
            Form4Seeder::class,
            Form3Seeder::class,
            Form5Seeder::class,
            ITServiceSeeder::class,
            IncidentSeeder::class,
            DroppedJobOrderSeeder::class,
            FailedProposalJobOrderSeeder::class,
            ClosedJobOrderSeeder::class,
            PerformanceCategorySeeder::class,
            PerformanceRatingSeeder::class,
            TeamLeaderPerformanceSeeder::class,
            EmployeePerformanceSeeder::class,
            JobOrderCorrectionSeeder::class,
        ]);
    }
}
