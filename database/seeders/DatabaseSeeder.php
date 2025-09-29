<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('avatars');
        Storage::disk('local')->deleteDirectory('it_services/reports');
        Storage::disk('local')->deleteDirectory('it_services/temp');

        DB::disableQueryLog();

        $this->call([
            PositionSeeder::class,
            RolesAndPermissionsSeeder::class,
            EmployeeSeeder::class,
            TruckSeeder::class,
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
            EmployeePerformanceSeeder::class,
            JobOrderCorrectionSeeder::class,
            UserSeeder::class,
            AdminPrivilegesUserSeeder::class,
            PerformanceSummarySeeder::class,
            EmployeeRatingSeeder::class,
            // AnnualReportSeeder::class,
        ]);

        DB::enableQueryLog();
    }
}
