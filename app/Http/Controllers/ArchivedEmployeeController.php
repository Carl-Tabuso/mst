<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArchivedEmployeeController extends Controller
{
    public function __construct(private EmployeeService $employeeService) {}

    public function index(Request $request): Response
    {
        $data = Employee::onlyTrashed()->with('position')->paginate(10)->toResourceCollection();

        return Inertia::render('archives/employees/Index', compact('data'));
    }

    public function update(Employee $employee): RedirectResponse
    {
        $this->employeeService->restoreArchivedEmployee($employee);

        $message = __('responses.restore.employee', ['employee' => $employee->full_name]);

        return back()->with(compact('message'));
    }

    public function bulkRestore(Request $request): RedirectResponse
    {
        $archivedEmployeeIds = $request->input('employeeIds', []);

        $this->employeeService->bulkRestoreArchivedEmployees($archivedEmployeeIds);

        $message = __('responses.batch_restore.employee', ['count' => count($archivedEmployeeIds)]);

        return back()->with(compact('message'));
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->employeeService->permanentlyDeleteEmployee($employee);

        $message = __('responses.permanent_delete.employee', ['employee' => $employee->full_name]);

        return back()->with(compact('message'));
    }
}
