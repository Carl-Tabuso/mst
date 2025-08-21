<?php

namespace App\Http\Controllers;

use App\Exports\JobOrderCorrectionExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportJobOrderCorrectionController extends Controller
{
    public function __invoke(Request $request)
    {
        $date = now()->toDateString();

        $fileName = sprintf('%s_%s.%s', $date, 'corrections', 'xlsx');

        $correctionIds = $request->array('correctionIds');

        return Excel::download(new JobOrderCorrectionExport($correctionIds), $fileName);
    }
}
