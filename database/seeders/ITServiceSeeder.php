<?php

namespace Database\Seeders;

use App\Models\ITService;
use App\Models\JobOrder;
use Illuminate\Database\Seeder;

class ITServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobOrder::factory()->count(5)
            ->for(ITService::factory(), 'serviceable')
            ->create();
    }
}
