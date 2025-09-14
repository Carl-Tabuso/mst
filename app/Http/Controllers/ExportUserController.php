<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportUserController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        $userIds = $request->input('userIds', []);

        $fileName = sprintf('%s_%s.%s', 'users', today()->toDateString(), 'xlsx');

        return Excel::download(new UserExport($userIds), $fileName);
    }
}
