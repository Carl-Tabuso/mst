<?php

namespace Database\Seeders;

use App\Models\Form5;
use App\Models\JobOrder;
use Illuminate\Database\Seeder;

class Form5Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobOrder::factory()->count(3)
            ->for(Form5::factory(), 'serviceable')
            ->create();
    }
}
