<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeDataExport;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\Position;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

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
        $search  = $filters['search']   ?? '';
        $perPage = $filters['per_page'] ?? 10;

        $employees = $this->employeeService->getAllEmployees(
            perPage: (int) $perPage,
            search: $search,
            filters: $filters
        );

        $positions = Position::orderBy('name')->get(['id', 'name']);

        return Inertia::render('employee-management/Index', [
            'data' => [
                'data' => EmployeeResource::collection($employees),
                'meta' => [
                    'current_page' => $employees->currentPage(),
                    'last_page'    => $employees->lastPage(),
                    'per_page'     => $employees->perPage(),
                    'total'        => $employees->total(),
                ],
            ],
            'emptySearchImg' => asset('images/empty-search.svg'),
            'filters'        => $filters,
            'positions'      => $positions,
        ]);
    }

    public function create()
    {
        $positions = Position::orderBy('name')->get();

        return Inertia::render('employee-management/Create', [
            'positions' => $positions,
        ]);
    }

    public function export(Request $request)
    {
        try {
            $positionIds = $request->input('filters.positions', []);

            $positionNames = [];
            if (!empty($positionIds)) {
                $positionNames = Position::whereIn('id', $positionIds)
                    ->pluck('name')
                    ->toArray();
            }

            $filters = [
                'positions' => $positionNames,
                'search' => $request->input('filters.search', ''),
            ];

            $employees = $this->employeeService->getEmployeesForRatingsExport($filters);

            if (!empty($filters['search'])) {
                $employees = $this->applySearch($employees, $filters['search']);
            }

            $filename = $this->generateExportFilename($filters);

            return Excel::download(
                new EmployeeDataExport($employees, $filters),
                $filename,
                \Maatwebsite\Excel\Excel::XLSX,
                [
                    'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]
            );
        } catch (\Exception $e) {
            Log::error('Export failed', [
                'error'   => $e->getMessage(),
                'user_id' => auth()->id(),
                'filters' => $filters ?? [],
            ]);

            return back()->with('error', 'Export failed. Please try again.');
        }
    }

    private function applySearch($employees, $search)
    {
        $searchTerm = strtolower(trim($search));

        return $employees->filter(function ($employee) use ($searchTerm) {
            return str_contains(strtolower($employee->full_name), $searchTerm) ||
                str_contains(strtolower($employee->position), $searchTerm) ||
                str_contains((string) $employee->id, $searchTerm);
        })->values();
    }

    private function generateExportFilename($filters)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');

        $parts = [];
        if (!empty($filters['positions'])) {
            $positionSlugs = array_map(function ($name) {
                return Str::slug(strtolower($name));
            }, $filters['positions']);

            $parts[] = 'positions-' . implode('-', $positionSlugs);
        }
        if (!empty($filters['search'])) {
            $parts[] = 'search-' . preg_replace('/[^a-zA-Z0-9]/', '', substr($filters['search'], 0, 10));
        }

        $filterSuffix = $parts ? '_' . implode('_', $parts) : '';

        return "employee_ratings_export_{$timestamp}{$filterSuffix}.xlsx";
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
    public function bulkArchive(Request $request)
    {
        $request->validate([
            'employeeIds' => 'required|array',
            'employeeIds.*' => 'exists:employees,id',
        ]);

        try {
            $employeeIds = $request->input('employeeIds');
            $result = $this->employeeService->bulkArchiveEmployees($employeeIds);

            return redirect()
                ->route('employee-management.index')
                ->with('success', count($employeeIds) . ' employee(s) archived successfully.');
        } catch (\Exception $e) {
            Log::error('Bulk archive failed', [
                'error' => $e->getMessage(),
                'employee_ids' => $request->input('employee_ids'),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to archive employees. Please try again.');
        }
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
            'employee'  => new EmployeeResource($employee),
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
                'id'   => $e->id,
                'name' => "{$e->first_name} {$e->last_name}",
            ]);

        return request()->inertia()
            ? inertia('Data/Employees', ['employees' => $employees])
            : response()->json(['employees' => $employees]);
    }
}
