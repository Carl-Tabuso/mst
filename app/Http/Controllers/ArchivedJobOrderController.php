<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use App\Services\JobOrderService;
use Illuminate\Http\RedirectResponse;

class ArchivedJobOrderController extends Controller
{
    public function __construct(private JobOrderService $service) {}

    public function index(Request $request): Response
    {
        $perPage = $request->integer('per_page', 10);

        $search = $request->input('search', '');

        $filters = $request->input('filters', []);

        $data = $this->service->getAllJobOrders($perPage, $search, $filters, true);

        return Inertia::render('archives/job-orders/Index', compact('data'));
    }

    public function update(JobOrder $jobOrder): RedirectResponse
    {
        // dd($jobOrder);
        $this->service->restoreArchivedJobOrder($jobOrder);

        $message = __('responses.restore.ticket', ['ticket' => $jobOrder->ticket]);

        return back()->with(compact('message'));
    }
}
