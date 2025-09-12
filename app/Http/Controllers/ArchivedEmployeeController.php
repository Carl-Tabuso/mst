<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArchivedEmployeeController extends Controller
{
    public function __construct(private EmployeeService $employeeService) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);

        $search = $request->input('search', '');

        $data = Employee::query()
            ->onlyTrashed()
            ->with('position')
            ->where(function (Builder $query) use ($search) {
                $query
                    ->whereAny([
                        'first_name',
                        'middle_name',
                        'last_name',
                        'suffix',
                        'contact_number',
                    ], 'like', "%{$search}%")
                    ->orWhereRaw("concat_ws(' ', first_name, middle_name, last_name, suffix) like ?", "%{$search}%")
                    ->orWhereHas('position', function (Builder $subQuery) use ($search) {
                        $subQuery->whereLike('name', $search);
                    });
            })
            ->latest(new Employee()->getDeletedAtColumn())
            ->paginate($perPage)
            ->withQueryString()
            ->toResourceCollection();

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
