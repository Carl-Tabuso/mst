<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportEmployeeController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $employeeIds = $request->input('employeeIds', []);

        $fileName = sprintf('%s_%s.%s', 'employees', today()->toDateString(), 'xlsx');

        return Excel::download(new EmployeeExport($employeeIds), $fileName);
    }
}
