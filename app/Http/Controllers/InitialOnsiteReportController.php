<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\StoreInitialOnsiteReportRequest;
use App\Models\InitialOnsiteReport;
use App\Models\ITService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class InitialOnsiteReportController extends Controller
{
    public function create(ITService $iTService): InertiaResponse
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

    public function showFile(ITService $iTService, InitialOnsiteReport $initialOnsite)
    {
        $filePath = sprintf('it_services/reports/%s', $initialOnsite->file_hash);

        $fileUrl = Storage::temporaryUrl($filePath, now()->addMinutes(5));

        return redirect()->to($fileUrl);
    }
}
