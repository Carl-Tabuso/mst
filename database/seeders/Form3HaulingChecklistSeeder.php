<?php

namespace Database\Seeders;

use App\Models\Form3Hauling;
use Illuminate\Database\Seeder;

class Form3HaulingChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Form3Hauling::all()->each(
            fn ($hauling) => $hauling->checklist()->create()->checkAllFields()
        );
    }
}
