<?php

namespace App\Services;

use App\Filters\ApplyDateOfArchivalRange;
use App\Filters\FilterOnlyArchived;
use App\Filters\User\FilterRole;
use App\Filters\User\SearchDetails;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Str;

class UserService
{
public function getAllUsers($search = '', $role = null, $sort = null, $perPage = 10)
{
    $query = User::with(['employee' => function ($query) {
        $query->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'position_id', 'created_at', 'updated_at');
    }])->select('users.*');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('email', 'like', "%{$search}%")
                ->orWhereHas('employee', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
        });
    }

    if ($role) {
        if (is_array($role)) {
            $query->whereHas('employee', function ($q) use ($role) {
                $q->whereIn('position_id', $role);
            });
        } else {
            $query->whereHas('employee', function ($q) use ($role) {
                $q->where('position_id', $role);
            });
        }
    }

    if ($sort) {
        [$column, $direction] = explode(':', $sort);

        if ($column === 'name') {
            $query->join('employees', 'users.employee_id', '=', 'employees.id')
                ->orderBy('employees.last_name', $direction)
                ->orderBy('employees.first_name', $direction);
        } else {
            $query->orderBy($column === 'created_at' ? 'users.created_at' : $column, $direction);
        }
    } else {
        $query->orderBy('users.created_at', 'desc');
    }

    return $query->with(['employee.position', 'roles'])->paginate($perPage);
}
    public function getArchivedUsers(?int $perPage = 10, ?string $search = '', ?array $filters = []): mixed
    {
        $archivedAtColumn = new User()->getDeletedAtColumn();

        $pipes = [
            new FilterOnlyArchived,
            new ApplyDateOfArchivalRange($archivedAtColumn, $filters),
            new SearchDetails($search),
            new FilterRole($filters),
        ];

        return Pipeline::send(User::query())
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage, $archivedAtColumn) {
                return $query
                    ->latest($archivedAtColumn)
                    ->paginate($perPage)
                    ->withQueryString()
                    ->toResourceCollection();
            });
    }

    public function createUser(array $validated)
    {
        return DB::transaction(function () use ($validated) {
            $password = Str::random(12);

            $user = User::create([
                'employee_id' => $validated['employee_id'],
                'email'       => $validated['email'],
                'password'    => Hash::make($password),
            ]);

            return [
                'user'     => $user,
                'password' => $password,
            ];
        });
    }

    public function updateUserProfile(User $user, array $validated)
    {
        return DB::transaction(function () use ($user, $validated) {
            $user->employee()->update([
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
            ]);

            $user->update(['email' => $validated['email']]);

            return $user;
        });
    }

    public function updateUserRole(User $user, int $positionId)
    {
        return $user->employee()->update([
            'position_id' => $positionId,
        ]);
    }

    public function deactivateUser(User $user)
    {
        return $user->delete();
    }

    public function restoreArchivedUser(User $user): bool
    {
        return $user->restore();
    }

    public function bulkRestoreArchivedUsers(array $userIds): mixed
    {
        return User::query()
            ->onlyTrashed()
            ->whereIn('id', $userIds)
            ->restore();
    }

    public function permanentlyDeleteUser(User $user): ?bool
    {
        return $user->forceDelete();
    }
}
