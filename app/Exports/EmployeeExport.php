<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExport implements FromCollection, WithHeadings
{
    public function __construct(private array $employeeIds) {}

    public function headings(): array
    {
        return [
            'First Name',
            'Middle Name',
            'Last Name',
            'Suffix',
            'Position',
            'Date of Birth',
            'Contact Number',
            'Date Created',
            'Date Archived',
        ];
    }

    public function collection(): Collection
    {
        return Employee::query()
            ->withTrashed()
            ->with('position')
            ->whereIn('id', $this->employeeIds)
            ->get()
            ->map(fn (Employee $employee) => [
                $employee->first_name,
                $employee?->middle_name,
                $employee->last_name,
                $employee?->suffix,
                $employee->position->name,
                $employee->date_of_birth,
                $employee->contact_number,
                $employee->created_at,
                $employee->archived_at,
            ]);
    }
}
