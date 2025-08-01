<?php

namespace App\Http\Controllers;

use App\Exports\ActivityLogExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportActivityLogController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $date = now()->toDateString();

        $fileName = "activitylogs_{$date}.xlsx";

        return Excel::download(new ActivityLogExport, $fileName);
    }
}
