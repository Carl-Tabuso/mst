<?php

namespace App\Traits;

use App\Models\Employee;
use App\Models\Position;

trait RandomEmployee
{
    public function getByPosition(string $name)
    {
        $position = Position::firstOrCreate(['name' => $name]);

        if ($position->employees->isEmpty()) {
            return Employee::factory()->create(['position_id' => $position->id]);
        }
        
        return $position->employees->random();
    }
}