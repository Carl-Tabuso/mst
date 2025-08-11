<?php

namespace App\Http\Controllers;

use App\Services\AnnualReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FrontlinerRankingsExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportFrontlinerRankingsController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $year = $request->integer('year', 2025);

        $fileName = "{$year}_frontliners.xlsx";

        return Excel::download(new FrontlinerRankingsExport($year), $fileName);
    }
}
