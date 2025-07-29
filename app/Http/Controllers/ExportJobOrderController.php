<?php

namespace App\Http\Controllers;

use App\Exports\JobOrderExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportJobOrderController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $date = now()->toDateString();

        $fileName = "joborder_{$date}.xlsx";

        return Excel::download(new JobOrderExport($request->array('jobOrderIds')), $fileName);
    }
}
