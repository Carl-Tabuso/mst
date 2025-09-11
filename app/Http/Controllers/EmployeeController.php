<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\Position;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index(Request $request)
    {
        $filters = $request->input('filters', []);
        $search = $filters['search'] ?? '';
        $perPage = $filters['per_page'] ?? 10;

        $employees = $this->employeeService->getAllEmployees(
            perPage: (int) $perPage,
            search: $search,
            filters: $filters
        );

        return Inertia::render('employee-management/Index', [
            'data' => [
                'data' => EmployeeResource::collection($employees),
                'meta' => [
                    'current_page' => $employees->currentPage(),
                    'last_page' => $employees->lastPage(),
                    'per_page' => $employees->perPage(),
                    'total' => $employees->total(),
                ],
            ],
            'emptySearchImg' => asset('images/empty-search.svg'),
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $positions = Position::orderBy('name')->get();

        return Inertia::render('employee-management/Create', [
            'positions' => $positions,
        ]);
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            $this->employeeService->createEmployee($request->validated());

            return redirect()
                ->route('employee-management.index')
                ->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating employee: ' . $e->getMessage());
        }
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'emergencyContact',
            'employmentDetails',
            'compensation',
            'account',
            'position',
        ]);

        return Inertia::render('employee-management/Edit', [
            'employee' => new EmployeeResource($employee),
        ]);
    }

    public function edit(Employee $employee)
    {
        $positions = Position::orderBy('name')->get();
        $employee->load([
            'emergencyContact',
            'employmentDetails',
            'compensation',
            'account',
            'position',
        ]);

        return Inertia::render('employee-management/Edit', [
            'employee' => new EmployeeResource($employee),
            'positions' => $positions,
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $this->employeeService->updateEmployee($employee, $request->validated());

            return redirect()
                ->route('employee-management.index')
                ->with('success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();

            return redirect()
                ->route('employee-management.index')
                ->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }

    public function dropdown()
    {
        $employees = Employee::select('id', 'first_name', 'last_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'name' => "{$e->first_name} {$e->last_name}",
            ]);

        return request()->inertia()
            ? inertia('Data/Employees', ['employees' => $employees])
            : response()->json(['employees' => $employees]);
    }
}
