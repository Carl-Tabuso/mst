<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\StoreWasteManagementRequest;
use App\Http\Requests\UpdateWasteManagementRequest;
use App\Http\Resources\JobOrderResource;
use App\Models\Employee;
use App\Models\Form3AssignedPersonnel;
use App\Models\Form3Hauling;
use App\Models\Form3HaulingChecklist;
use App\Models\Form4;
use App\Models\JobOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
            'creator'     => ['account:avatar'],
            'serviceable' => [
                'dispatcher' => ['account:avatar'],
                'appraisers' => ['account:avatar'],
                'form3'      => [
                    'haulings' => [
                        'checklist',
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

        $employees = Inertia::optional(fn () => Employee::with('account:avatar')->get()->toResourceCollection());

        return Inertia::render('job-orders/waste-managements/Edit', compact('jobOrder', 'employees'));
    }

    public function update(UpdateWasteManagementRequest $request, Form4 $form4)
    {
        // [/] if status is for appraisal - appraisers and appraised date should be not null and set to for viewing and dispatcher to currently auth user.
        // [/] if status is successful - payment type, bid bond, or number, payment date, and date approved must be not null and set to pre-hauling.
        // [/] if pre-hauling - check also if hauling duration is not null and set to personnel assignemtn
        // [] if status is for personnel assignment - today and future date's personnel and haulers are not null or empty array
        // [] if status is for safety inspection - today's safety inspection checklist are complete

        if ($request->enum('status', JobOrderStatus::class) === JobOrderStatus::ForAppraisal) {
            $validator = Validator::make($request->all(), [
                'appraisers'     => ['required', 'array'],
                'appraised_date' => ['required', 'date'],
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $validated = (object) $validator->safe();

            DB::transaction(function () use ($request, $form4, $validated) {
                $form4->update([
                    'form_dispatcher' => $request->user()->id,
                ]);

                $form4->form3()->create([
                    'appraised_date' => $validated->date('appraised_date'),
                ]);

                $form4->jobOrder()->update([
                    'status' => JobOrderStatus::ForViewing,
                ]);

                $appraisers = array_map(
                    fn ($appraiser) => $appraiser['id'], $validated->array('appraisers')
                );
                $form4->appraisers()->sync($appraisers);
            });

            return redirect()->back()->withInput([
                'success' => 'Nays',
            ]);
        }

        if ($request->enum('status', JobOrderStatus::class) === JobOrderStatus::Successful) {
            $validator = Validator::make($request->all(), [
                'payment_date'  => ['required', 'date'],
                'or_number'     => ['required', 'string'],
                'bid_bond'      => ['required', 'string'],
                'payment_type'  => ['required', 'string'],
                'approved_date' => ['required', 'date'],
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $validated = (object) $validator->safe();

            DB::transaction(function () use ($form4, $validated) {
                $form4->form3()->update([
                    'payment_type'  => $validated->input('payment_type'),
                    'approved_date' => $validated->date('approved_date'),
                ]);

                $form4->update([
                    'payment_date' => $validated->date('payment_date'),
                    'or_number'    => $validated->input('or_number'),
                    'bid_bond'     => $validated->input('bid_bond'),
                ]);

                $form4->jobOrder()->update([
                    'status' => JobOrderStatus::PreHauling,
                ]);
            });

            return redirect()->back()->withInput();
        }

        if ($request->enum('status', JobOrderStatus::class) === JobOrderStatus::PreHauling) {
            $validator = Validator::make($request->all(), [
                'from' => ['required', 'date'],
                'to'   => ['required', 'date'],
            ])->setCustomMessages([
                'from' => 'Start date is required.',
                'to'   => 'Finish date is required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $validated = (object) $validator->safe();

            DB::transaction(function () use ($form4, $validated) {
                $from = $validated->date('from');
                $to   = $validated->date('to');

                $form4->form3()->update([
                    'from' => $from,
                    'to'   => $to,
                ]);

                $duration = (int) $from->diffInDays($to) + 1;

                $haulingInserts = array_map(fn ($range) => [
                    'form3_id' => $form4->form3->id,
                    'date'     => $from->copy()->addDays($range),
                ], range(0, $duration - 1));

                Form3Hauling::insert($haulingInserts);

                $preHaulingInserts = $form4->form3->haulings->map(fn ($hauling) => [
                    'form3_hauling_id' => $hauling->id,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ])->toArray();

                Form3HaulingChecklist::insert($preHaulingInserts);

                Form3AssignedPersonnel::insert($preHaulingInserts);

                $form4->jobOrder()->update([
                    'status' => JobOrderStatus::ForPersonnelAssignment,
                ]);
            });

            return redirect()->back()->withInput();
        }

        if ($request->enum('status', JobOrderStatus::class) === JobOrderStatus::ForPersonnelAssignment) {
            $filteredHaulings = array_filter($request->array('haulings'),
                fn ($haul) => Carbon::parse($haul['date'])->gte(today())
            );

            $mappedHaulings = array_map(function ($hauling) {
                $personnel = $hauling['assignedPersonnel'];

                return [
                    'id' => $hauling['id'],
                    'assignedPersonnel' => [
                        'team_leader'    => $personnel['teamLeader']['id']    ?? null,
                        'team_driver'    => $personnel['teamDriver']['id']    ?? null,
                        'safety_officer' => $personnel['safetyOfficer']['id'] ?? null,
                        'team_mechanic'  => $personnel['teamMechanic']['id']  ?? null,
                    ],
                    'haulers' => array_map(fn ($h) => $h['id'], $hauling['haulers'])
                ];
            }, $filteredHaulings);

            $form4->form3->haulings()
                ->where('date', '>=', today())
                ->get()
                ->each(function ($hauling) use ($mappedHaulings) {
                    $mapped = array_find($mappedHaulings, fn ($mh) => $hauling->id === $mh['id']);

                    $hauling->assignedPersonnel()->update($mapped['assignedPersonnel']);

                    empty($mapped['haulers'])
                        ? $hauling->haulers()->detach()
                        : $hauling->haulers()->sync($mapped['haulers']);
                });

            return redirect()->back()->withInput();
        }

        return redirect()->route('job_order.index'); // ->with() messages should be
    }
}
