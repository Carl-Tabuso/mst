<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    const POSITIONS = [
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

        foreach (self::POSITIONS as $position) {
            $data[] = [
                'name' => $position,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        Position::insert($data);
    }
}
