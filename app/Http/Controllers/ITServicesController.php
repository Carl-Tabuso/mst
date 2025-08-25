<?php
namespace App\Http\Controllers;

use App\Enums\ITServiceStatus;
use App\Enums\JobOrderStatus;
use App\Enums\MachineStatus;
use App\Models\Employee;
use App\Models\ITService;
use App\Models\ITServiceCorrection;
use App\Models\ITServiceReport;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ITServicesController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);

        $search = "%{$request->input('search')}%";

        $filters = null;

        if ($request->has('filters')) {
            $filters = (object) $request->array('filters');
        }

        $hasItServiceStatuses = isset($filters->itServiceStatuses) && count($filters->itServiceStatuses) > 0;

        $hasDateOfServiceRange =
        isset($filters->fromDateOfService, $filters->toDateOfService) &&
        $filters?->fromDateOfService && $filters?->toDateOfService;

        $jobOrders = JobOrder::query()
            ->where('serviceable_type', 'it_service')
            ->when($hasItServiceStatuses, function ($q) use ($filters) {
                $q->whereHasMorph('serviceable', [ITService::class], function ($itServiceQuery) use ($filters) {
                    $itServiceQuery->byStatus($filters->itServiceStatuses);
                });
            })
            ->when($hasDateOfServiceRange, fn($q) => $q->whereBetween('date_time', [
                Carbon::parse($filters->fromDateOfService)->startOfDay(),
                Carbon::parse($filters->toDateOfService)->endOfDay(),
            ]))
            ->when($search, fn($q) => $q->where(function ($sq) use ($search) {
                $sq->whereLike('client', $search)
                    ->orWhereLike('address', $search)
                    ->orWhereLike('contact_no', $search)
                    ->orWhereLike('contact_person', $search)
                    ->orWhereHas('creator', fn($subQuery) => $subQuery->whereLike('first_name', $search)
                            ->orWhereLike('middle_name', $search)
                            ->orWhereLike('last_name', $search)
                            ->orWhereLike('suffix', $search));
            }))
            ->with(['creator', 'serviceable.reports'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->toResourceCollection();

        return Inertia::render('itservices/page/Index', compact('jobOrders'));
    }

    public function createInitial()
    {
        $employee = auth()->user()->employee()->with('position')->first();

        if (! in_array(strtolower($employee->position->name ?? ''), ['frontliner', 'dispatcher'])) {
            abort(403, 'Unauthorized');
        }

        $technicians = Employee::whereHas('position', fn($q) => $q->where('name', 'Technician'))
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'suffix']);

        $machineTypes = ['Printer', 'Laptop', 'Desktop', 'Server', 'Other'];

        $machineStatuses = collect(MachineStatus::cases())->map(fn($status) => [
            'value' => $status->value,
            'label' => $status->getLabel(),
        ])->values();

        return Inertia::render('itservices/page/Form', [
            'technicians'     => $technicians,
            'machineTypes'    => $machineTypes,
            'machineStatuses' => $machineStatuses,
        ]);
    }

    public function storeInitial(Request $request)
    {
        $validated = $request->validate([
            'client'          => 'required|string|max:255',
            'address'         => 'required|string|max:255',
            'department'      => 'required|string|max:255',
            'contact_no'      => 'required|string|max:255',
            'contact_person'  => 'required|string|max:255',
            'date'            => 'required|date',
            'time'            => 'required',
            'technician_id'   => 'required|exists:employees,id',
            'machine_type'    => 'required|string|max:255',
            'model'           => 'required|string|max:255',
            'serial_no'       => 'required|string|max:255',
            'tag_no'          => 'required|string|max:255',
            'machine_problem' => 'nullable|string',
        ]);

        $dateTime = $validated['date'] . ' ' . $validated['time'];

        $itServiceStatus = ITServiceStatus::ForCheckUp;

        $itService = ITService::create([
            'technician_id'   => $validated['technician_id'],
            'machine_type'    => $validated['machine_type'],
            'model'           => $validated['model'],
            'serial_no'       => $validated['serial_no'],
            'tag_no'          => $validated['tag_no'],
            'machine_problem' => $validated['machine_problem'] ?? null,
            'status'          => $itServiceStatus,
        ]);

        $jobOrderStatus = match ($itServiceStatus) {
            ITServiceStatus::ForCheckUp => JobOrderStatus::InProgress,
            ITServiceStatus::ForFinalService => JobOrderStatus::InProgress,
            ITServiceStatus::Completed => JobOrderStatus::Completed,
        };

        $jobOrder = new JobOrder([
            'client'         => $validated['client'],
            'address'        => $validated['address'],
            'department'     => $validated['department'],
            'contact_no'     => $validated['contact_no'],
            'contact_person' => $validated['contact_person'],
            'date_time'      => $dateTime,
            'status'         => $jobOrderStatus->value,
            'created_by'     => auth()->id(),
        ]);

        $jobOrder->serviceable()->associate($itService);
        $jobOrder->save();

        return redirect()->route('job_order.it_service.index')->with('success', 'IT Service created successfully!');
    }

    public function editInitial(JobOrder $jobOrder)
    {
        $employee = auth()->user()->employee()->with('position')->first();

        if (! in_array(strtolower($employee->position->name ?? ''), ['frontliner', 'dispatcher'])) {
            abort(403, 'Unauthorized');
        }

        // Make sure the serviceable is ITService
        if (! $jobOrder->serviceable instanceof ITService) {
            abort(404, 'Not an IT Service job order');
        }

        $technicians = Employee::whereHas('position', fn($q) => $q->where('name', 'Technician'))
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'suffix']);

        $machineTypes = ['Printer', 'Laptop', 'Desktop', 'Server', 'Other'];

        $machineStatuses = collect(MachineStatus::cases())->map(fn($status) => [
            'value' => $status->value,
            'label' => $status->getLabel(),
        ])->values();

        return Inertia::render('itservices/page/EditInitialForm', [
            'jobOrderId'      => $jobOrder->id,
            'itServiceId'     => $jobOrder->serviceable->id,
            'technicians'     => $technicians,
            'machineTypes'    => $machineTypes,
            'machineStatuses' => $machineStatuses,
            'jobOrder'        => $jobOrder->only([
                'id',
                'client',
                'address',
                'department',
                'contact_no',
                'contact_person',
                'date_time',
            ]),
            'itService'       => $jobOrder->serviceable->only([
                'id',
                'technician_id',
                'machine_type',
                'model',
                'serial_no',
                'tag_no',
                'machine_problem',
            ]),
            'isEdit'          => true,
        ]);

    }

    public function viewInitial(JobOrder $jobOrder)
    {
        $itService = $jobOrder->serviceable;

        $itService->load('technician');

        return Inertia::render('itservices/page/ViewInitial', [
            'jobOrder'  => $jobOrder,
            'itService' => $itService,
        ]);
    }

    public function createFirstOnsite(Request $request, JobOrder $jobOrder)
    {
        $itService = ITService::findOrFail($request->service_id);

        $machineStatuses = collect(MachineStatus::cases())->map(fn($status) => [
            'label' => $status->getLabel(),
            'value' => $status->value,
        ]);

        return Inertia::render('itservices/page/FirstOnsiteForm', [
            'jobOrderId'      => $jobOrder->id,
            'serviceId'       => $itService->id,
            'jobOrderNumber'  => $jobOrder->job_order_no,
            'machineStatuses' => $machineStatuses,
        ]);
    }

    public function storeFirstOnsite(Request $request)
    {

        Log::info('Request data:', $request->all());

        if ($request->hasFile('attached_file')) {
            Log::info('File info:', [
                'name' => $request->file('attached_file')->getClientOriginalName(),
                'size' => $request->file('attached_file')->getSize(),
            ]);
        }

        $validated = $request->validate([
            'job_order_id'      => 'required|exists:job_orders,id',
            'it_service_id'     => 'required|exists:it_services,id',
            'onsite_type'       => 'required|in:initial',
            'service_performed' => 'required|string',
            'recommendation'    => 'required|string',
            'machine_status'    => 'required|string',
            'attached_file'     => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:5120',
        ]);

        if ($request->hasFile('attached_file')) {
            $validated['attached_file'] = $request->file('attached_file')->store('it_service_reports', 'private');
        }

        ITServiceReport::create($validated);

        $itService = ITService::findOrFail($validated['it_service_id']);
        $itService->update([
            'status' => ITServiceStatus::ForFinalService,
        ]);

        return redirect()->route('job_order.it_service.index')->with('success', 'First Onsite Report submitted!');
    }

    public function editFirstOnsite(JobOrder $jobOrder, $reportId)
    {
        Log::info('Edit First Onsite: Request received.', [
            'job_order_id' => $jobOrder->id,
            'report_id'    => $reportId,
        ]);

        $employee = auth()->user()->employee()->with('position')->first();
        Log::info('Authenticated employee loaded.', [
            'employee_id' => $employee->id,
            'position'    => $employee->position->name ?? null,
        ]);

        $itService = $jobOrder->serviceable;
        $itService->load('technician');

        $report = ITServiceReport::where('id', $reportId)
            ->where('it_service_id', $itService->id)
            ->where('onsite_type', 'Initial')
            ->first();

        if (! $report) {
            Log::warning('Edit First Onsite: Report not found.', [
                'report_id'     => $reportId,
                'it_service_id' => $itService->id,
            ]);
            abort(404, 'First onsite report not found.');
        }

        if (! in_array(strtolower($employee->position->name ?? ''), ['frontliner', 'dispatcher'])) {
            Log::warning('Unauthorized access attempt.', [
                'employee_id' => $employee->id,
                'position'    => $employee->position->name ?? null,
            ]);
            abort(403, 'Unauthorized');
        }

        if ($report->it_service_id !== $jobOrder->serviceable->id) {
            Log::error('Report does not match JobOrder.', [
                'report_it_service_id'   => $report->it_service_id,
                'expected_it_service_id' => $jobOrder->serviceable->id,
            ]);
            abort(404, 'Report does not belong to this job order');
        }

        if ($report->onsite_type !== 'Initial') {
            Log::error('Report onsite type mismatch.', [
                'expected' => 'initial',
                'actual'   => $report->onsite_type,
            ]);
            abort(404, 'Not a first onsite report');
        }

        $technicians = Employee::whereHas('position', fn($q) => $q->where('name', 'Technician'))
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'suffix']);

        $machineTypes = ['Printer', 'Laptop', 'Desktop', 'Server', 'Other'];

        $machineStatuses = collect(MachineStatus::cases())->map(fn($status) => [
            'label' => $status->getLabel(),
            'value' => $status->value,
        ]);

        $logData = [
            'job_order_id'  => $jobOrder->id,
            'report_id'     => $report->id,
            'employee_id'   => $employee->id,
            'it_service_id' => $itService->id,
        ];
        Log::info('Edit First Onsite: Data ready for rendering.', $logData);

        return Inertia::render('itservices/page/EditFirstOnsiteForm', [
            'jobOrderId'      => $jobOrder->id,
            'jobOrderNumber'  => $jobOrder->job_order_no,
            'serviceId'       => $itService->id,
            'report'          => $report,
            'technicians'     => $technicians,
            'machineTypes'    => $machineTypes,
            'machineStatuses' => $machineStatuses,
            'jobOrder'        => $jobOrder->only([
                'id',
                'client',
                'address',
                'department',
                'contact_no',
                'contact_person',
                'date_time',
                'job_order_no',
            ]),
            'itService'       => $itService->only([
                'id',
                'technician_id',
                'machine_type',
                'model',
                'serial_no',
                'tag_no',
                'machine_problem',
                'status',
            ]),
            'existingReport'  => $report->only([
                'id',
                'service_performed',
                'recommendation',
                'machine_status',
                'attached_file',
            ]),
            'isEdit'          => true,
        ]);
    }

    public function viewFirstOnsite(JobOrder $jobOrder, $reportId)
    {
        $itService = $jobOrder->serviceable;

        $itService->load('technician');

        $report = ITServiceReport::where('id', $reportId)
            ->where('it_service_id', $itService->id)
            ->where('onsite_type', 'initial')
            ->firstOrFail();

        $status = $report->machine_status instanceof MachineStatus
        ? $report->machine_status
        : MachineStatus::from($report->machine_status);

        return Inertia::render('itservices/page/ViewFirstOnsite', [
            'jobOrder'           => $jobOrder,
            'itService'          => $itService,
            'report'             => $report,
            'machineStatusLabel' => $report->machine_status ? $status->getLabel() : null,
        ]);
    }

    public function downloadAttachment(JobOrder $jobOrder, $reportId)
    {
        if (!auth()->check()) {
            Log::error('Download attempt without authentication', ['report_id' => $reportId]);
            abort(401, 'Authentication required');
        }

        $user = auth()->user();
        if (!$user) {
            Log::error('User not found in downloadAttachment');
            abort(401, 'User not found');
        }

        $employee = $user->employee()->with('position')->first();
        if (!$employee) {
            Log::error('Employee not found for user', ['user_id' => $user->id]);
            abort(403, 'Employee record not found');
        }

        if (!$employee->position) {
            Log::error('Position not found for employee', ['employee_id' => $employee->id]);
            abort(403, 'Employee position not found');
        }
        
        $allowedPositions = ['frontliner', 'dispatcher', 'technician', 'admin'];
        if (!in_array(strtolower($employee->position->name ?? ''), $allowedPositions)) {
            Log::warning('Unauthorized file access attempt', [
                'user_id' => $user->id,
                'employee_id' => $employee->id,
                'position' => $employee->position->name ?? 'null',
                'report_id' => $reportId
            ]);
            abort(403, 'Unauthorized to access this file');
        }

        $itService = $jobOrder->serviceable;
        if (!$itService) {
            Log::error('IT Service not found for job order', ['job_order_id' => $jobOrder->id]);
            abort(404, 'IT Service not found for this job order');
        }
        
        $report = ITServiceReport::where('id', $reportId)
            ->where('it_service_id', $itService->id)
            ->whereIn('onsite_type', ['initial', 'final'])
            ->first();

        if (!$report) {
            Log::error('Report not found', [
                'report_id' => $reportId,
                'it_service_id' => $itService->id,
                'job_order_id' => $jobOrder->id
            ]);
            abort(404, 'Report not found');
        }

        if (!$report->attached_file) {
            Log::warning('No file attached to report', ['report_id' => $reportId]);
            abort(404, 'No file attached to this report');
        }

        if (!Storage::disk('private')->exists($report->attached_file)) {
            Log::error('File not found in storage', [
                'file_path' => $report->attached_file,
                'report_id' => $reportId
            ]);
            abort(404, 'File not found in storage');
        }

        $originalName = $this->getOriginalFileName($report, $jobOrder);
        
        Log::info('File download initiated', [
            'user_id' => $user->id,
            'employee_id' => $employee->id,
            'report_id' => $reportId,
            'job_order_id' => $jobOrder->id,
            'file_path' => $report->attached_file
        ]);
        
        return Storage::disk('private')->download($report->attached_file, $originalName);
    }

    private function getOriginalFileName($report, $jobOrder)
    {
        $extension = pathinfo($report->attached_file, PATHINFO_EXTENSION);
        $reportType = ucfirst(strtolower($report->onsite_type));
        $jobOrderNo = $jobOrder->job_order_no ?? 'Unknown';
        
        return "{$reportType}_Report_{$jobOrderNo}.{$extension}";
    }

    public function createLastOnsite(Request $request, JobOrder $jobOrder)
    {
        $itService = ITService::findOrFail($request->service_id);

        $machineStatuses = collect(MachineStatus::cases())->map(fn($status) => [
            'label' => $status->getLabel(),
            'value' => $status->value,
        ]);

        return Inertia::render('itservices/page/LastOnsiteForm', [
            'jobOrderId'      => $jobOrder->id,
            'serviceId'       => $itService->id,
            'machineStatuses' => $machineStatuses,
        ]);
    }

    public function storeLastOnsite(Request $request)
    {
        $validated = $request->validate([
            'job_order_id'         => 'required|exists:job_orders,id',
            'it_service_id'        => 'required|exists:it_services,id',
            'onsite_type'          => 'required|in:final',
            'service_performed'    => 'required|string',
            'parts_replaced'       => 'required|string',
            'final_remark'         => 'required|string',
            'final_machine_status' => 'required|string',

        ]);

        logger()->info('Final Onsite:', [
            'job_order_id'  => $request->input('job_order_id'),
            'it_service_id' => $request->input('it_service_id'),
        ]);

        ITServiceReport::create($validated);

        $itService = ITService::findOrFail($validated['it_service_id']);
        $itService->update([
            'status' => ITServiceStatus::Completed,
        ]);

        return redirect()
            ->route('job_order.it_service.index')
            ->with('success', 'Final Onsite Report submitted successfully!');
    }

    public function editLastOnsite(JobOrder $jobOrder)
    {
        $itService = $jobOrder->serviceable;

        $firstOnsiteReport = ITServiceReport::where('it_service_id', $itService->id)
            ->whereRaw('LOWER(onsite_type) = ?', ['initial'])
            ->first();

        $finalReport = ITServiceReport::where('it_service_id', $itService->id)
            ->whereRaw('LOWER(onsite_type) = ?', ['final'])
            ->first();

        if (! $finalReport) {
            Log::error('Final onsite report not found.', [
                'job_order_id'  => $jobOrder->id,
                'it_service_id' => $itService->id,
            ]);
            abort(404, 'Final onsite report not found');
        }

        $technicians = Employee::whereHas('position', fn($q) => $q->where('name', 'Technician'))
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'suffix']);

        $machineTypes = ['Printer', 'Laptop', 'Desktop', 'Server', 'Other'];

        return Inertia::render('itservices/page/EditLastOnsite', [
            'jobOrderId'          => $jobOrder->id,
            'jobOrderNumber'      => $jobOrder->job_order_no,
            'serviceId'           => $itService->id,
            'reportId'            => $finalReport->id,
            'firstOnsiteReport'   => $firstOnsiteReport ? [
                'id'                => $firstOnsiteReport->id,
                'service_performed' => $firstOnsiteReport->service_performed,
                'recommendation'    => $firstOnsiteReport->recommendation,
                // For initial reports, machine_status is stored in 'machine_status' column
                'machine_status'    => $firstOnsiteReport->machine_status,
                'attached_file'     => $firstOnsiteReport->attached_file,
            ] : null,
            'existingFinalReport' => [
                'id'                   => $finalReport->id,
                'service_performed'    => $finalReport->service_performed,
                'parts_replaced'       => $finalReport->parts_replaced,
                'final_remark'         => $finalReport->final_remark,
                // For final reports, machine_status is stored in 'final_machine_status' column
                'final_machine_status' => $finalReport->final_machine_status,
                'attached_file'        => $finalReport->attached_file,
            ],
            'jobOrder'            => $jobOrder->only([
                'id', 'client', 'address', 'department', 'contact_no', 'contact_person', 'date_time', 'job_order_no',
            ]),
            'itService'           => $itService->only([
                'id', 'technician_id', 'machine_type', 'model', 'serial_no', 'tag_no', 'machine_problem', 'status',
            ]),
            'technicians'         => $technicians,
            'machineTypes'        => $machineTypes,
            'firstOnsiteStatuses' => MachineStatus::forFirstOnsite(),
            'finalOnsiteStatuses' => MachineStatus::forFinalOnsite(),
            'isEdit'              => true,
        ]);
    }

    public function viewLastOnsite(JobOrder $jobOrder)
    {
        $itService = $jobOrder->serviceable;

        $itService->load('technician');

        $reports = ITServiceReport::where('it_service_id', $itService->id)->get();

        $firstOnsite = $reports->firstWhere('onsite_type', 'Initial');
        $finalOnsite = $reports->firstWhere('onsite_type', 'Final');

        $mapReport = function ($report, $machineStatusSource = null) {
            if (! $report) {
                return null;
            }

            $status = null;

            if ($machineStatusSource?->machine_status) {
                $status = $machineStatusSource->machine_status instanceof MachineStatus
                ? $machineStatusSource->machine_status
                : MachineStatus::tryFrom($machineStatusSource->machine_status);
            }

            return [
                 ...$report->toArray(),
                'machineStatusLabel' => $status?->getLabel() ?? 'N/A',
            ];
        };

        return Inertia::render('itservices/page/ViewFinalOnsite', [
            'jobOrder'          => $jobOrder,
            'itService'         => $itService,
            'firstOnsiteReport' => $mapReport($firstOnsite),
            'finalOnsiteReport' => $mapReport($finalOnsite, $firstOnsite), // <- Pass machine status from Initial
        ]);
    }

    public function requestCorrection(Request $request, JobOrder $jobOrder)
    {
        $itService = $jobOrder->serviceable;

        if (! $itService) {
            Log::error('No IT Service found for this Job Order.', [
                'job_order_id' => $jobOrder->id,
            ]);
            return back()->withErrors(['error' => 'No IT Service record found.']);
        }

        try {
            $validated = $request->validate([
                'fields'                     => ['required', 'array'],
                'fields.*'                   => ['nullable'],
                'fields.attached_file'       => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
                'fields.first_attached_file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ]);

            Log::info('Validation passed', ['validated' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        $fields = $validated['fields'];

        $attachedFile      = $request->file('fields.attached_file');
        $firstAttachedFile = $request->file('fields.first_attached_file');

        Log::info('File upload check', [
            'attached_file'           => $attachedFile,
            'first_attached_file'     => $firstAttachedFile,
            'has_attached_file'       => $request->hasFile('fields.attached_file'),
            'has_first_attached_file' => $request->hasFile('fields.first_attached_file'),
            'all_files'               => $request->allFiles(),
        ]);

        if ($attachedFile) {
            $attachedFilePath        = $attachedFile->store('it_service_reports', 'public');
            $fields['attached_file'] = $attachedFilePath;
            Log::info('Final report file uploaded', ['path' => $attachedFilePath]);
        }

        if ($firstAttachedFile) {
            $firstAttachedFilePath         = $firstAttachedFile->store('it_service_reports', 'private');
            $fields['first_attached_file'] = $firstAttachedFilePath;
            Log::info('First onsite file uploaded', ['path' => $firstAttachedFilePath]);
        }

        $currentData = $this->buildComprehensiveCurrentData($jobOrder, $itService);

        $before = [];
        $after  = [];

        foreach ($fields as $key => $newValue) {
            if ($key === 'date' || $key === 'time') {
                continue;
            }

            if ($newValue instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }

            $currentValue = $currentData[$key] ?? null;

            $currentValueStr = $currentValue instanceof \BackedEnum  ? $currentValue->value : (string) $currentValue;
            $newValueStr     = $newValue instanceof \BackedEnum  ? $newValue->value : (string) $newValue;

            if ($currentValueStr !== $newValueStr) {
                $before[$key] = $currentValue;
                $after[$key]  = $newValue;
            }
        }

        if (isset($fields['date'], $fields['time'])) {
            $newDateTime     = $fields['date'] . ' ' . $fields['time'];
            $currentDateTime = $jobOrder->date_time ? Carbon::parse($jobOrder->date_time)->format('Y-m-d H:i') : null;

            if ($currentDateTime !== $newDateTime) {
                $before['date_time'] = $currentDateTime;
                $after['date_time']  = $newDateTime;
            }
        }

        if (empty($before) && empty($after)) {
            Log::info('No changes detected, skipping correction save.', [
                'it_service_id' => $itService->id,
                'user_id'       => auth()->id(),
            ]);
            return back()->with('message', 'No changes detected.');
        }

        ITServiceCorrection::create([
            'it_service_id' => $itService->id,
            'user_id'       => auth()->id(),
            'properties'    => [
                'before' => $before,
                'after'  => $after,
            ],
            'is_approved'   => null,
        ]);

        Log::info('Correction saved.', [
            'it_service_id' => $itService->id,
            'user_id'       => auth()->id(),
            'properties'    => ['before' => $before, 'after' => $after],
        ]);

        return redirect()
            ->route('job_order.it_service.index')
            ->with('message', 'Correction submitted for approval.');

    }

    private function buildComprehensiveCurrentData(JobOrder $jobOrder, ITService $itService): array
    {
        $currentData = [
            // From JobOrder
            'client'          => $jobOrder->client,
            'address'         => $jobOrder->address,
            'department'      => $jobOrder->department,
            'contact_no'      => $jobOrder->contact_no,
            'contact_person'  => $jobOrder->contact_person,
            // From ITService
            'technician_id'   => $itService->technician_id,
            'machine_type'    => $itService->machine_type,
            'model'           => $itService->model,
            'serial_no'       => $itService->serial_no,
            'tag_no'          => $itService->tag_no,
            'machine_problem' => $itService->machine_problem,
        ];

        // Get First Onsite Report (onsite_type = 'initial')
        $firstOnsiteReport = ITServiceReport::where('it_service_id', $itService->id)
            ->where('onsite_type', 'initial')
            ->first();

        if ($firstOnsiteReport) {
            $currentData = array_merge($currentData, [
                'service_performed' => $firstOnsiteReport->service_performed,
                'recommendation'    => $firstOnsiteReport->recommendation,
                'machine_status'    => $firstOnsiteReport->machine_status,
                'attached_file'     => $firstOnsiteReport->attached_file,
            ]);
        }

        // Get Last Onsite Report (onsite_type = 'final')
        $lastOnsiteReport = ITServiceReport::where('it_service_id', $itService->id)
            ->where('onsite_type', 'final')
            ->first();

        if ($lastOnsiteReport) {
            $currentData = array_merge($currentData, [
                'service_performed' => $lastOnsiteReport->service_performed,
                'parts_replaced'    => $lastOnsiteReport->parts_replaced,
                'final_remark'      => $lastOnsiteReport->final_remark,
            ]);
        }

        Log::info('Built comprehensive current data', [
            'it_service_id'     => $itService->id,
            'has_first_onsite'  => $firstOnsiteReport !== null,
            'has_last_onsite'   => $lastOnsiteReport !== null,
            'current_data_keys' => array_keys($currentData),
        ]);

        return $currentData;
    }

    public function archive(Request $request)
    {
        $ids = $request->input('ids', []);
        if (! is_array($ids) || empty($ids)) {
            return back()->with('error', 'No IT Service job orders selected.');
        }

        $jobOrders = JobOrder::where('serviceable_type', 'it_service')
            ->whereIn('id', $ids)
            ->get();

        foreach ($jobOrders as $jobOrder) {
            $jobOrder->delete();
        }

        return back()->with('success', 'Selected IT Service job orders archived successfully.');
    }
}
