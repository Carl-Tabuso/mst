<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\StoreWasteManagementRequest;
use App\Http\Requests\UpdateWasteManagementRequest;
use App\Http\Resources\JobOrderResource;
use App\Models\Employee;
use App\Models\Form4;
use App\Models\JobOrder;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WasteManagementController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreWasteManagementRequest $request)
    {
        $validated = $request->safe()->merge([
            'created_by' => $request->user()->id,
        ]);

        DB::transaction(function () use ($validated) {
            $wasteManagement = Form4::create();

            $wasteManagement->jobOrder()->create($validated->toArray());
        });

        return redirect()->route('job_order.index');
    }

    public function edit(JobOrder $ticket): Response
    {
        $loads = $ticket->load([
            'serviceable' => [
                'appraisers' => ['account:avatar'],
                'form3'      => [
                    'haulings' => [
                        'haulers'           => ['account:avatar'],
                        'assignedPersonnel' => [
                            'teamLeader'    => ['account:avatar'],
                            'teamDriver'    => ['account:avatar'],
                            'safetyOfficer' => ['account:avatar'],
                            'teamMechanic'  => ['account:avatar'],
                        ],
                    ],
                ],
            ],
        ]);

        $jobOrder = JobOrderResource::make($loads);

        $employees = Employee::with('account:employee_id')->get()->toResourceCollection();

        return Inertia::render('job-orders/waste-managements/Edit', compact('jobOrder', 'employees'));
    }

    public function update(UpdateWasteManagementRequest $request, Form4 $form4)
    {
        // is date/time of service the timestamp of a newly logged job order?
        // disable first section (form 4)
        //

        // needs update
        DB::transaction(function () use ($request, $form4) {
            $validated = $request->safe();

            $form4->jobOrder()->update([
                'status' => $validated->enum('status', JobOrderStatus::class),
            ]);

            $form4->updateOrCreate([
                'payment_date' => $validated->date('payment_date'),
                'bid_bond'     => $validated->input('bid_bond'),
                'or_number'    => $validated->input('or_number'),
            ]);

            $form4->appraisers()->sync($validated->array('appraisers'));

            $form3 = $form4->form3()->updateOrCreate([
                'appraised_date' => $validated->date('appraised_date'),
                'approved_date'  => $validated->date('approved_date'),
                'truck_no'       => $validated->input('truck_no'),
                'payment_type'   => $validated->input('payment_type'),
                'team_leader'    => $validated->input('team_leader'),
                'team_driver'    => $validated->input('team_driver'),
                'safety_officer' => $validated->input('safety_officer'),
                'team_mechanic'  => $validated->input('team_mechanic'),
            ]);

            $form3->haulers()->sync($validated->array('haulers'));
        });

        return redirect()->route('job_order.index'); // ->with() messages should be
    }
}
