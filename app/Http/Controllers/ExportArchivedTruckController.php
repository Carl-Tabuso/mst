<?php

namespace App\Http\Controllers;

use App\Exports\ArchivedTruckExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportArchivedTruckController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $truckIds = $request->input('truckIds', []);

        $fileName = sprintf('%s.%s', 'archived_trucks', 'xlsx');

        return Excel::download(new ArchivedTruckExport($truckIds), $fileName);
    }
}
