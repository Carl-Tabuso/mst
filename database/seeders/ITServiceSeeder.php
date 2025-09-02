<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\ITService;
use App\Models\JobOrder;
use Illuminate\Database\Seeder;

class ITServiceSeeder extends Seeder
{
    public function run(): void
    {
        JobOrder::factory()
            ->for(ITService::factory(), 'serviceable')
            ->status(JobOrderStatus::ForCheckup)
            ->create();
    }
}
