<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppraisalRequest;
use App\Http\Requests\UpdateAppraisalRequest;
use App\Models\Form4;
use App\Services\WasteManagementService;
use Illuminate\Http\RedirectResponse;

class AppraisalController extends Controller
{
    public function __construct(private WasteManagementService $wasteManagementService) {}

    public function store(StoreAppraisalRequest $request, Form4 $form4): RedirectResponse
    {
        $validated = array_merge($request->validated(), [
            'user' => $request->user(),
        ]);

        $response = $this->wasteManagementService->handleForAppraisal($form4, $validated);

        $message = __('responses.status_update', ['status' => $response]);

        return back()->with(compact('message'));
    }

    public function update(UpdateAppraisalRequest $request, Form4 $form4): RedirectResponse
    {
        $validated = $request->validated();

        $this->wasteManagementService->updateOrCreateAppraisalInformation($form4, $validated);

        return back()->with(['message' => __('responses.change')]);
    }
}
