<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductionDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('avatars');
        Storage::disk('local')->deleteDirectory('it_services/reports');
        Storage::disk('local')->deleteDirectory('it_services/temp');

        DB::transaction(function () {
            $this->call([
                PositionSeeder::class,
                RolesAndPermissionsSeeder::class,
                ITAdminSeeder::class,
                HumanResourceSeeder::class,
            ]);
        });
    }
}
