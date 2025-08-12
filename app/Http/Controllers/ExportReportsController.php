<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportReportsController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $year = $request->integer('year');

        $fileName = sprintf('%s_%s', $year, 'reports.xlsx');

        return Excel::download(new ReportsExport($year), $fileName);
    }
}
