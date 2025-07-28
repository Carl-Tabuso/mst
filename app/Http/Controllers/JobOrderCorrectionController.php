<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\StoreJobOrderCorrectionRequest;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobOrderCorrectionController extends Controller
{
    public function store(StoreJobOrderCorrectionRequest $request, JobOrder $ticket)
    {
        $validated = (object) $request->validated();

        $ticket->fill([
            'date_time'        => $request->safe()->date('date_time')->toDateTimeString(),
            'client'           => $validated->client,
            'address'          => $validated->address,
            'department'       => $validated->department,
            'contact_position' => $validated->contact_position,
            'contact_person'   => $validated->contact_person,
            'contact_no'       => $validated->contact_no,
        ]);

        $updateableModels = [$ticket];

        $canUpdateProposal = in_array($ticket->status, JobOrderStatus::getCanRequestCorrectionStages());

        if ($canUpdateProposal) {
            $ticket->serviceable->fill([
                'payment_date' => $request->safe()->date('payment_date')->toDateString(),
                'or_number'    => $validated->or_number,
                'bid_bond'     => $validated->bid_bond,
            ]);

            $ticket->serviceable->form3->fill([
                'payment_type'  => $validated->payment_type,
                'approved_date' => $request->safe()->date('approved_date')->toDateString(),
            ]);

            array_push($updateableModels, $ticket->serviceable, $ticket->serviceable->form3);
        }

        $data = [];

        foreach ($updateableModels as $model) {
            foreach($model->getDirty() as $key => $value) {
                $data['properties']['before'][$key] = $model->getOriginal($key);
                $data['properties']['after'][$key] = $value;
            }
        }

        $data['reason'] = $validated->reason;
        dd($data);

        $ticket->corrections()->create($data);

        return back()->with(['message' => __('responses.correction')]);
    }

    public function update(Request $request, JobOrderCorrection $correction)
    {
        //
    }
}
