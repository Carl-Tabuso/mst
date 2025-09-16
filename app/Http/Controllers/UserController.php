<?php

namespace App\Http\Controllers;

use App\Mail\NewUserCredentials;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return $this->getUsersData($request);
        }

        return Inertia::render('user-management/index');
    }

    public function settings(User $user)
    {
        $user->load(['employee', 'employee.position']);

        return inertia('user-management/settings', [
            'user' => [
                'id'          => $user->id,
                'first_name'  => $user->employee->first_name,
                'last_name'   => $user->employee->last_name,
                'email'       => $user->email,
                'avatar'      => $user->avatar,
                'created_at'  => $user->created_at->format('F j, Y'),
                'deleted_at'  => $user->deleted_at,
                'position'    => $user->employee->position->name ?? 'N/A',
                'position_id' => $user->employee->position_id,
            ],
            'positions' => Position::all()->map(fn ($p) => [
                'id'   => $p->id,
                'name' => $p->name,
            ]),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,'.$user->id,
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->employee()->update([
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
            ]);

            $user->update(['email' => $validated['email']]);
        });

        return back()->with('success', 'Profile updated successfully');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'position_id' => 'required|exists:positions,id',
        ]);

        $user->employee()->update([
            'position_id' => $request->position_id,
        ]);

        return back()->with('success', 'Role updated successfully');
    }

    public function deactivate(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Account deactivated successfully']);
    }

    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->employee()->delete();
            $user->forceDelete();
        });

        return response()->json(['message' => 'Account permanently deleted']);
    }

    public function getUsersData(Request $request)
    {
        $query = User::with(['employee' => function ($query) {
            $query->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'position_id', 'created_at', 'updated_at');
        }])
            ->select('users.*');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->search}%")
                    ->orWhereHas('employee', function ($q) use ($request) {
                        $q->where('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$request->search}%"]);
                    });
            });
        }

        if ($request->status) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNotNull('deleted_at');
            }
        }

        if ($request->sort) {
            [$column, $direction] = explode(':', $request->sort);

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

        $perPage = $request->per_page ?? 10;
        $users   = $query->paginate($perPage);

        $transformedUsers = $users->getCollection()->map(function ($user) {
            return [
                'id'         => $user->id,
                'email'      => $user->email,
                'avatar'     => $user->avatar,
                'employee'   => $user->employee,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'deleted_at' => $user->deleted_at,
            ];
        });

        return response()->json([
            'data'         => $transformedUsers,
            'current_page' => $users->currentPage(),
            'last_page'    => $users->lastPage(),
            'per_page'     => $users->perPage(),
            'total'        => $users->total(),
        ]);

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:users,employee_id',
            'email'       => 'required|email|unique:users,email',
            'role'        => 'required|string|max:255',
        ]);

        try {
            $password = \Illuminate\Support\Str::random(12);

            $user = User::create([
                'employee_id' => $validated['employee_id'],
                'email'       => $validated['email'],
                'password'    => Hash::make($password),
                'role'        => $validated['role'],
            ]);

            Mail::to($user->email)->send(new NewUserCredentials($user, $password));

            return response()->json([
                'message' => 'User created successfully. Credentials have been sent to the email.',
                'user'    => $user,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
}
