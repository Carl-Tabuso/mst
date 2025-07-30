<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\CreateCancelledJobOrderRequest;
use App\Models\JobOrder;
use Illuminate\Http\RedirectResponse;

class CancelledJobOrderController extends Controller
{
    public function create(CreateCancelledJobOrderRequest $request, JobOrder $jobOrder): RedirectResponse
    {
        $jobOrder->cancel()->create([
            'reason' => $request->safe()->input('reason'),
        ]);

        $jobOrder->update([
            'status' => $request->safe()->enum('status', JobOrderStatus::class),
        ]);

        return back();
    }
}
