<?php

namespace App\Http\Controllers;

use App\Enums\MachineStatus;
use App\Models\Employee;
use App\Models\ITService;
use App\Models\JobOrder;
use App\Models\MachineInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        $hasStatuses = isset($filters->statuses) && count($filters->statuses) > 0;

        $hasDateOfServiceRange =
            isset($filters->fromDateOfService, $filters->toDateOfService) &&
            $filters?->fromDateOfService                                  && $filters?->toDateOfService;

        $jobOrders = JobOrder::query()
            ->where('serviceable_type', 'it_service')
            ->when($hasStatuses, fn ($q) => $q->ofStatuses($filters->statuses))
            ->when($hasDateOfServiceRange, function ($q) use ($filters) {
                return $q->whereBetween('date_time', [
                    Carbon::parse($filters->fromDateOfService)->startOfDay(),
                    Carbon::parse($filters->toDateOfService)->endOfDay(),
                ]);
            })
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($sq) use ($search) {
                    $sq->whereLike('client', $search)
                        ->orWhereLike('address', $search)
                        ->orWhereLike('contact_no', $search)
                        ->orWhereLike('contact_person', $search)
                        ->orWhereHas('creator', function ($subQuery) use ($search) {
                            return $subQuery->whereLike('first_name', $search)
                                ->orWhereLike('middle_name', $search)
                                ->orWhereLike('last_name', $search)
                                ->orWhereLike('suffix', $search);
                        });
                });
            })
            ->with(['creator'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->toResourceCollection();

        return Inertia::render('itservices/page/Index', compact('jobOrders'));
    }

    public function create()
    {
        $user     = auth()->user();
        $employee = $user->employee()->with('position')->first();

        $allowedPositions = ['Frontliner', 'Dispatcher'];

        $employeePositionName = strtolower($employee->position->name ?? '');

        $allowed = collect($allowedPositions)
            ->map(fn ($pos) => strtolower($pos))
            ->contains($employeePositionName);

        if (! $allowed) {
            abort(403, 'Unauthorized');
        }

        $technicians = Employee::whereHas('position', function ($q) {
            $q->where('name', 'Technician');
        })->get(['id', 'first_name', 'middle_name', 'last_name', 'suffix']);

        $machineTypes = [
            'Printer', 'Laptop', 'Desktop', 'Server', 'Other',
        ];

        $machineStatuses = collect(MachineStatus::cases())->map(fn ($status) => [
            'value' => $status->value,
            'label' => $status->getLabel(),
        ])->values();

        return inertia('itservices/page/Form', [
            'technicians'      => $technicians,
            'machineTypes'     => $machineTypes,
            'existingServices' => MachineInfo::select('machine_type', 'model', 'serial_no', 'tag_no')->get(),
            'machineStatuses'  => $machineStatuses,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client'         => 'required|string|max:255',
                'address'        => 'required|string|max:255',
                'department'     => 'required|string|max:255',
                'contact_no'     => 'required|string|max:255',
                'contact_person' => 'required|string|max:255',
                'date'           => 'required|date',
                'time'           => 'required',
                'status'         => 'required|string|max:255',

                'technicians'   => 'required|array|min:1',
                'technicians.*' => 'exists:employees,id',

                'machine_infos'                     => 'required|array|min:1',
                'machine_infos.*.machine_type'      => 'required|string|max:255',
                'machine_infos.*.model'             => 'required|string|max:255',
                'machine_infos.*.serial_no'         => 'required|string|max:255',
                'machine_infos.*.tag_no'            => 'required|string|max:255',
                'machine_infos.*.machine_problem'   => 'nullable|string',
                'machine_infos.*.service_performed' => 'nullable|string',
                'machine_infos.*.recommendation'    => 'nullable|string',
                'machine_infos.*.machine_status'    => 'nullable|string|max:255',
            ]);

            $itService = ITService::create();

            $itService->technicians()->attach($validated['technicians']);

            foreach ($validated['machine_infos'] as $machine) {
                $itService->machineInfos()->create([
                    'machine_type'      => $machine['machine_type'],
                    'model'             => $machine['model'],
                    'serial_no'         => $machine['serial_no'],
                    'tag_no'            => $machine['tag_no'],
                    'machine_problem'   => $machine['machine_problem']   ?? null,
                    'service_performed' => $machine['service_performed'] ?? null,
                    'recommendation'    => $machine['recommendation']    ?? null,
                    'machine_status'    => $machine['machine_status']    ?? null,
                ]);
            }

            $dateTime = $validated['date'].' '.$validated['time'];

            $jobOrder = new JobOrder([
                'client'         => $validated['client'],
                'address'        => $validated['address'],
                'department'     => $validated['department'],
                'contact_no'     => $validated['contact_no'],
                'contact_person' => $validated['contact_person'],
                'date_time'      => $dateTime,
                'status'         => $validated['status'],
                'created_by'     => auth()->id(),
            ]);
            $jobOrder->serviceable()->associate($itService);
            $jobOrder->save();

        } catch (\Throwable $e) {
            throw $e;
        }

        return redirect()->back()->with('success', 'IT Service created successfully!');
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
