<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionDatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
