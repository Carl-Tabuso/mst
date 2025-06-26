<?php

namespace App\Http\Controllers;

use App\Exports\JobOrderExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportJobOrderController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $date = now()->toDateString();

        $fileName = "joborder_{$date}.xlsx";

        return Excel::download(new JobOrderExport, $fileName);
    }
}
