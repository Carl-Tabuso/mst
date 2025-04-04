<?php

namespace Database\Seeders;

use App\Models\Form3;
use Illuminate\Database\Seeder;

class Form3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Form3::factory()->create();
    }
}
