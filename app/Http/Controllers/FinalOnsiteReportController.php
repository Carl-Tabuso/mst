<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinalOnsiteReportRequest;
use App\Models\ITService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FinalOnsiteReportController extends Controller
{
    public function create(ITService $iTService)
    {
        return Inertia::render('itservices/page/FinalOnsiteForm', [
            'service' => $iTService->load('jobOrder.creator')->toResource(),
        ]);
    }

    public function store(StoreFinalOnsiteReportRequest $request, ITService $iTService)
    {
        DB::transaction(function () use ($request, $iTService) {
            $iTService->finalOnsiteReport()->create($request->validated());

            $iTService->jobOrder->markAsCompleted();

            return true;
        });

        return redirect()->route('job_order.it_service.index');
    }
}
