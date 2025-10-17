<?php

namespace Database\Seeders;

use App\Models\Truck;
use Illuminate\Database\Seeder;

class ArchivedTruckSeeder extends Seeder
{
    public function run(): void
    {
        activity()->disableLogging();

        Truck::factory()
            ->count(20)
            ->trashed()
            ->create();
    }
}
