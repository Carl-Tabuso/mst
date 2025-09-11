<?php

namespace App\Http\Controllers;

use App\Enums\ActivityLogName;
use App\Enums\JobOrderCorrectionRequestStatus;
use App\Enums\JobOrderServiceType;
use App\Http\Requests\StoreJobOrderCorrectionRequest;
use App\Http\Requests\UpdateJobOrderCorrectionRequest;
use App\Http\Resources\JobOrderCorrectionResource;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use App\Services\JobOrderCorrectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function store(StoreJobOrderCorrectionRequest $request, JobOrder $ticket): RedirectResponse
    {
        $data = $request->validated();

        $correction = $this->service->storeJobOrderCorrection($data, $ticket);

        return redirect()->route('job_order.correction.show', [
            'correction' => $correction->id,
        ]);
    }

    public function show(JobOrderCorrection $correction): Response
    {
        $serviceType = $correction->jobOrder->serviceable_type;

        $correction->load([
            'jobOrder' => [
                'creator' => ['account'],
                'cancel',
                'serviceable' => [
                    ...($serviceType === JobOrderServiceType::Form4 ? [
                        'form3',
                    ] : []),
                    ...($serviceType === JobOrderServiceType::ITService ? [
                        'technician' => ['account'],
                        'initialOnsiteReport',
                        'finalOnsiteReport',
                    ] : []),
                    ...($serviceType === JobOrderServiceType::Form5 ? [
                        'items',
                        'assignedPerson',
                    ] : []),
                ],
            ],
        ]);

        $subFolder = match ($serviceType) {
            JobOrderServiceType::Form4     => 'waste-managements',
            JobOrderServiceType::ITService => 'it-services',
            JobOrderServiceType::Form5     => 'other-services',
        };

        $component = sprintf('%s/%s/%s', 'corrections', $subFolder, 'Show');

        return Inertia::render($component, [
            'data' => JobOrderCorrectionResource::make($correction),
        ]);
    }

    public function showInitialOnsiteReportTempFile(JobOrderCorrection $correction): RedirectResponse
    {
        $filePath = sprintf('it_services/temp/%s', $correction->properties['after']['file_hash']);

        $fileUrl = Storage::temporaryUrl($filePath, now()->addMinutes(5));

        return redirect()->to($fileUrl);
    }

    public function update(UpdateJobOrderCorrectionRequest $request, JobOrderCorrection $correction): RedirectResponse
    {
        $data = $request->validated();

        $this->service->updateJobOrderCorrection($data, $correction);

        $status = $request->safe()->enum('status', JobOrderCorrectionRequestStatus::class);

        $message = __('responses.correction_update', ['status' => $status->getLabel()]);

        return redirect()->route('job_order.correction.index')->with(compact('message'));
    }

    public function destroy(JobOrderCorrection $correction): RedirectResponse
    {
        $this->service->deleteJobOrderCorrection($correction);

        return redirect()->route('job_order.correction.index')
            ->with(['message' => __('responses.archive.correction', [
                'ticket' => $correction->jobOrder->ticket,
            ])]);
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $correctionIds = $request->array('correctionIds');

        activity()->withoutLogs(fn () => $this->service->batchDeleteJobOrderCorrections($correctionIds));

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
