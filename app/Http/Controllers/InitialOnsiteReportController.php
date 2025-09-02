<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Enums\MachineStatus;
use App\Http\Requests\StoreInitialOnsiteReportRequest;
use App\Models\ITService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InitialOnsiteReportController extends Controller
{
    public function create(ITService $itService)
    {
        return Inertia::render('itservices/page/InitialOnsiteForm', [
            'itService' => $itService->load('jobOrder.creator')->toResource(),
        ]);
    }

    public function store(StoreInitialOnsiteReportRequest $request, ITService $itService)
    {
        $reportFile = $request->file('report_file');

        $dbOperation = DB::transaction(function () use ($request, $itService, $reportFile) {
            $itService->initialOnsiteReport()->create([
                ...$request->validated(),
                'file_name' => $reportFile->getClientOriginalName(),
                'file_hash' => $reportFile->hashName(),
            ]);

            return $itService->jobOrder()->update([
                'status' => JobOrderStatus::ForFinalService,
            ]);
        });

        // idk if this ensures that the database operation is success before storing the file. - carl
        if ($dbOperation) {
            $reportFile->store('it_services/reports');
        }

        return redirect()->route('job_order.it_service.index');
    }
}
