<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\CreateCancelledJobOrderRequest;
use App\Models\JobOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CancelledJobOrderController extends Controller
{
    public function create(CreateCancelledJobOrderRequest $request, JobOrder $jobOrder): RedirectResponse
    {
        $status = $request->safe()->enum('status', JobOrderStatus::class);

        DB::transaction(function () use ($request, $jobOrder, $status) {
            $jobOrder->cancel()->create([
                'reason' => $request->safe()->input('reason'),
            ]);

            $jobOrder->update(['status' => $status]);
        });

        $message = __('responses.job_order.close', [
            'ticket' => $jobOrder->ticket,
            'status' => $status->getLabel(),
        ]);

        return back()->with(compact('message'));
    }
}
