<?php

namespace Database\Seeders;

use App\Models\ITService;
use Illuminate\Database\Seeder;

class ITServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ITService::factory(5)->create();
    }
}
