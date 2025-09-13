<?php

namespace App\Exports;

use App\Enums\UserRole;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
{
    public function __construct(private array $userIds) {}

    public function headings(): array
    {
        return [
            'Employee Name',
            'Email Address',
            'Date Verified',
            'Role',
            'Date Created',
            'Date Archived',
        ];
    }

    public function collection()
    {
        return User::query()
            ->withTrashed()
            ->with(['roles', 'employee'])
            ->whereIn('id', $this->userIds)
            ->get()
            ->map(fn (User $user) => [
                $user->employee->full_name,
                $user->email,
                $user?->email_verified_at,
                UserRole::from($user->getRoleNames()->first())->getLabel(),
                $user->created_at,
                $user->deleted_at,
            ]);
    }
}
