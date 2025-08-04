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
            RolesAndPermissionsSeeder::class,
            EmployeeSeeder::class,
            // Form4Seeder::class,
            Form3HaulingSeeder::class,
            Form3HaulingChecklistSeeder::class,
            Form3AssignedPersonnelSeeder::class,
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
            UserSeeder::class,
            PerformanceSummarySeeder::class,
            EmployeeRatingSeeder::class,
            AnnualReportSeeder::class,
        ]);
    }
}
