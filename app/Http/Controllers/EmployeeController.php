<?php

namespace App\Http\Controllers;

use App\Mail\NewUserCredentials;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function storeWithAccount(Request $request)
    {
        $validated = $request->validate([
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|max:255',
            'suffix'      => 'nullable|string|max:10',
            'position_id' => 'required|exists:positions,id',

            'email' => 'required|email|unique:users,email',
        ]);

        try {
            DB::beginTransaction();

            $employee = Employee::create([
                'first_name'  => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name'   => $validated['last_name'],
                'suffix'      => $validated['suffix'],
                'position_id' => $validated['position_id'],
            ]);

            $password = \Illuminate\Support\Str::random(12);

            $user = User::create([
                'employee_id' => $employee->id,
                'email'       => $validated['email'],
                'password'    => Hash::make($password),
            ]);

            Mail::to($user->email)->send(new NewUserCredentials($user, $password));
            DB::commit();

            return response()->json([
                'message'  => 'Employee and account created successfully',
                'employee' => $employee,
                'user'     => $user,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error creating employee and account',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }

    public function dropdown()
    {
        $employees = Employee::select('id', 'first_name', 'last_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn ($e) => [
                'id'   => $e->id,
                'name' => "{$e->first_name} {$e->last_name}",
            ]);

        if (request()->inertia()) {
            return inertia('Data/Employees', [
                'employees' => $employees,
            ]);
        }

        return response()->json(['employees' => $employees]);
    }
}
