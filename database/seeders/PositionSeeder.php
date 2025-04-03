<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PositionSeeder extends Seeder
{
    private static $positions = [
        'Team Leader',
        'Safety Officer',
        'Frontliner',
        'Human Resource',
        'Consultant',
        'Dispatcher',
        'Admin Tools Custodian',
        'Electrician',
        'Driver',
        'Engineer',
        'Facilitator',
        'Security Guard',
        'Mechanic',
        'Hauler',
        'Technician',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        collect(static::$positions)->each(function ($position) use (&$data) {
            $data[] = [
                'name' => $position,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });
        
        DB::table('positions')->insert($data);
    }
}
