<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWasteManagementRequest;
use App\Http\Resources\Form4Resource;
use App\Models\Employee;
use App\Models\Form4;
use App\Models\JobOrder;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

    public function show(JobOrder $jobOrder)
    {
        //
    }

    public function edit(JobOrder $jobOrder)
    {
        $loads = $jobOrder->load([
            'serviceable' => [
                'form3',
                'appraisers',
                'jobOrder'
            ]
        ]);

        $form4 = Form4Resource::make($loads->serviceable);

        $employees = Employee::with('position')->paginate(10)->toResourceCollection();

        return Inertia::render('job-orders/waste-managements/Edit', compact('form4', 'employees'));
    }
}
