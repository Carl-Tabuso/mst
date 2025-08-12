<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\StoreWasteManagementRequest;
use App\Http\Requests\UpdateWasteManagementRequest;
use App\Models\Form4;
use App\Models\JobOrder;
use App\Services\WasteManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WasteManagementController extends Controller
{
    public function __construct(private WasteManagementService $service) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);

        $search = $request->input('search', '');

        $filters = $request->input('filters', []);

        $data = $this->service->getAllWasteManagementJobOrders($perPage, $search, $filters);

        return Inertia::render('job-orders/waste-managements/Index', [
            'data'           => $data,
            'emptySearchImg' => asset('state/search-empty.svg'),
            'emptyJobOrders' => asset('state/task-empty.svg'),
        ]);
    }

    public function store(StoreWasteManagementRequest $request): RedirectResponse
    {
        $validated = $request->safe()->merge([
            'created_by' => $request->user()->id,
        ])->toArray();

        $data = $this->service->storeWasteManagement($validated);

        [$title, $description] = explode('|', __('responses.job_order.create', [
            'ticket' => $data->ticket,
        ]));

        return redirect()->route('job_order.waste_management.edit', [
            'ticket' => $data->ticket,
        ])->with(['message' => compact('title', 'description')]);
    }

    public function edit(JobOrder $ticket): Response
    {
        $data = $this->service->getWasteManagementData($ticket);

        return Inertia::render('job-orders/waste-managements/Edit', compact('data'));
    }

    public function update(UpdateWasteManagementRequest $request, Form4 $form4): RedirectResponse
    {
        $validated = array_merge($request->validated(), [
            'user' => $request->user(),
        ]);

        $response = $this->service->updateWasteManagement($validated, $form4);

        $status = $request->safe()->enum('status', JobOrderStatus::class);

        $message = $status === JobOrderStatus::HaulingInProgress
            ? __('responses.change')
            : __('responses.status_update', ['status' => $response]);

        return back()->with(compact('message'));
    }
}
