<?php

namespace App\Http\Controllers;

use App\Exports\TruckExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportTruckController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        return Excel::download(new TruckExport, 'trucks.xlsx');
    }
}
