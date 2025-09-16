<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

public function index(Request $request)
{
    $employees = Employee::whereDoesntHave('account')
        ->with('position')
        ->get();

    $filters = $request->filters ?? [];

    $users = $this->userService->getAllUsers(
        search: $filters['search']  ?? null,
        role: $filters['role']      ?? null, 
        sort: $filters['sort']      ?? null,
        perPage: $request->per_page ?? $filters['per_page'] ?? 10
    );

    return Inertia::render('user-management/index', [
        'data'      => UserResource::collection($users),
        'employees' => $employees,
        'roles'     => Position::all()->map(fn($p) => [ 
            'id' => $p->id,
            'name' => $p->name
        ]),
        'meta'      => [
            'current_page' => $users->currentPage(),
            'last_page'    => $users->lastPage(),
            'per_page'     => $users->perPage(),
            'total'        => $users->total(),
        ],
        'emptySearchImg' => asset('images/empty-search.svg'),
        'filters'        => $filters,
    ]);
}

    public function settings(User $user)
    {
        $user->load(['employee', 'employee.position']);

        return Inertia::render('user-management/settings', [
            'user' => [
                'id'         => $user->id,
                'email'      => $user->email,
                'created_at' => $user->created_at->format('F j, Y'),
                'deleted_at' => $user->deleted_at,
                'employee'   => [
                    'first_name'  => $user->employee->first_name,
                    'last_name'   => $user->employee->last_name,
                    'position'    => $user->employee->position->name ?? 'N/A',
                    'position_id' => $user->employee->position_id,
                ],
            ],
            'positions' => Position::all()->map(fn ($p) => [
                'id'   => $p->id,
                'name' => $p->name,
            ]),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $this->userService->updateUserProfile($user, $request->validated());

            return redirect()->route('users.index')->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating profile: '.$e->getMessage());
        }
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'position_id' => 'required|exists:positions,id',
        ]);

        try {
            $this->userService->updateUserRole($user, $request->position_id);

            return redirect()->route('users.index')->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating role: '.$e->getMessage());
        }
    }

    public function deactivate(User $user)
    {
        try {
            $this->userService->deactivateUser($user);

            return redirect()->route('users.index')->with('success', 'Account deactivated successfully');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Error deactivating account: '.$e->getMessage());
        }
    }

    public function restore(User $user)
    {
        try {
            $user->restore();

            return redirect()->route('users.index')->with('success', 'Account activated successfully');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Error activating account: '.$e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->permanentlyDeleteUser($user);

            return redirect()->route('users.index')->with('success', 'Account permanently deleted');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Error deleting account: '.$e->getMessage());
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $result   = $this->userService->createUser($request->validated());
            $user     = $result['user'];
            $password = $result['password'];

            $user->sendEmailVerificationNotificationWithPassword($password);

            return redirect()->route('users.index')->with('success', 'User created successfully. Verification email with credentials has been sent.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating user: '.$e->getMessage());
        }
    }

    public function show(User $user) {}

    public function edit(User $user) {}
}
