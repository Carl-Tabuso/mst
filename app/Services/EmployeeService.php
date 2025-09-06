<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    public function getEmployeesForDropdown()
    {
        return Employee::select('id', 'first_name', 'last_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn ($employee) => [
                'id'   => $employee->id,
                'name' => "{$employee->first_name} {$employee->last_name}",
            ]);
    }
}