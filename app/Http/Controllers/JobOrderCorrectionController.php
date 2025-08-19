<?php

namespace App\Http\Controllers;

use App\Enums\ActivityLogName;
use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Http\Requests\StoreJobOrderCorrectionRequest;
use App\Http\Requests\UpdateJobOrderCorrectionRequest;
use App\Http\Resources\JobOrderCorrectionResource;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use App\Services\JobOrderCorrectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class JobOrderCorrectionController extends Controller
{
    public function __construct(private JobOrderCorrectionService $service) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);

        $search = $request->input('search', '');

        $filters = $request->input('statuses', []);

        $data = $this->service->getAllJobOrderCorrections($perPage, $search, $filters);

        return Inertia::render('corrections/Index', compact('data'));
    }

    public function store(StoreJobOrderCorrectionRequest $request, JobOrder $ticket)
    {
        $validated = (object) $request->validated();

        $ticket->fill([
            'date_time'        => $request->safe()->date('date_time'),
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
                'payment_date' => $request->safe()->date('payment_date'),
                'or_number'    => $validated->or_number,
                'bid_bond'     => $validated->bid_bond,
            ]);

            $ticket->serviceable->form3->fill([
                'payment_type'  => $validated->payment_type,
                'approved_date' => $request->safe()->date('approved_date'),
            ]);

            array_push($updateableModels, $ticket->serviceable, $ticket->serviceable->form3);
        }

        $data = [];

        foreach ($updateableModels as $model) {
            foreach ($model->getDirty() as $key => $value) {
                $data['properties']['before'][$key] = $model->getOriginal($key);
                $data['properties']['after'][$key]  = $value;
            }
        }

        $data['reason'] = $validated->reason;

        $ticket->corrections()->create($data);

        return back()->with(['message' => __('responses.correction')]);
    }

    public function show(JobOrderCorrection $correction)
    {
        $correction->load([
            'jobOrder' => [
                'creator' => ['account:avatar'],
                'cancel',
                'serviceable' => ['form3'],
            ]
        ]);

        $subFolder = match ($correction->jobOrder->serviceable_type) {
            JobOrderServiceType::Form4 => 'waste-managements',
            JobOrderServiceType::ITService => 'it-services',
            JobOrderServiceType::Form5 => 'other-services',
        };

        $component = sprintf('%s/%s/%s', 'corrections', $subFolder, 'Show');

        return Inertia::render($component, [
            'data' => JobOrderCorrectionResource::make($correction)
        ]);
    }

    public function update(UpdateJobOrderCorrectionRequest $request, JobOrderCorrection $correction)
    {
        dd($request->all(), $correction);
    }

    public function destroy(Request $request, ?JobOrderCorrection $correction = null)
    {
        if ($correction) {
            $correction->delete();

            return redirect()->route('job_order.correction.index')
                ->with(['message' => __('responses.archive.correction', [
                    'ticket' => $correction->jobOrder->ticket,
                ])]);
        }

        $correctionIds = $request->array('correctionIds');

        activity()->withoutLogs(fn () => DB::transaction(fn () => JobOrderCorrection::destroy($correctionIds)));

        $user = $request->user();

        activity()
            ->useLog(ActivityLogName::TicketCorrectionBatchArchive->value)
            ->causedBy($user)
            ->event('deleted')
            ->withProperties([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log(__('activity.job_order.archived.batch', [
                'causer'       => $user->employee->full_name,
                'ticket_count' => count($correctionIds),
            ]));

        return back()->with(['message' => __('responses.batch_archive.correction', [
            'count' => count($correctionIds),
        ])]);
    }
}
