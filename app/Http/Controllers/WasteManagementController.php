<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWasteManagementRequest;
use App\Http\Requests\UpdateWasteManagementRequest;
use App\Http\Resources\Form4Resource;
use App\Models\Employee;
use App\Models\Form4;
use Illuminate\Http\Request;
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

    public function show(Form4 $form4)
    {
        //
    }

    public function edit(Request $request, Form4 $form4)
    {
        $loads = $form4->load([
            'form3' => [
                'teamLeader',
                'driver',
                'safetyOfficer',
                'mechanic',
                'haulers',
            ],
            'appraisers',
            'jobOrder',
        ]);

        $form4 = Form4Resource::make($loads);

        return Inertia::render('job-orders/waste-managements/Edit', [
            'form4'     => $form4,
            'employees' => Employee::all()->toResourceCollection(),
        ]);
    }

    public function update(UpdateWasteManagementRequest $request, Form4 $form4)
    {
        dd($request->all());
    }
}
