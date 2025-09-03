<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\StoreInitialOnsiteReportRequest;
use App\Models\ITService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class InitialOnsiteReportController extends Controller
{
    public function create(ITService $iTService): Response
    {
        return Inertia::render('itservices/page/InitialOnsiteForm', [
            'iTService' => $iTService->load('jobOrder.creator')->toResource(),
        ]);
    }

    public function store(StoreInitialOnsiteReportRequest $request, ITService $iTService): RedirectResponse
    {
        $reportFile = $request->file('report_file');

        $dbOperation = DB::transaction(function () use ($request, $iTService, $reportFile) {
            $data = [
                ...$request->validated(),
                'file_name' => $reportFile?->getClientOriginalName(),
                'file_hash' => $reportFile?->hashName(),
            ];

            $iTService->initialOnsiteReport()->create($data);

            return $iTService->jobOrder()->update([
                'status' => JobOrderStatus::ForFinalService,
            ]);
        });

        // idk if this ensures that the database operation is success before storing the file. - carl
        if ($dbOperation) {
            $reportFile?->store('it_services/reports');
        }

        return redirect()->route('job_order.it_service.index');
    }
}
