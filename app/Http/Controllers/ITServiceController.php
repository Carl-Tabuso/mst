<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreITServiceRequest;
use App\Models\Employee;
use App\Models\ITService;
use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ITServiceController extends Controller
{
    public function index(Request $request)
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
        $filters?->fromDateOfService                                  && $filters?->toDateOfService;

        $jobOrders = JobOrder::query()
            ->where('serviceable_type', 'it_service')
            ->when($hasItServiceStatuses, function ($q) use ($filters) {
                $q->whereHasMorph('serviceable', [ITService::class], function ($itServiceQuery) use ($filters) {
                    $itServiceQuery->byStatus($filters->itServiceStatuses);
                });
            })
            ->when($hasDateOfServiceRange, fn ($q) => $q->whereBetween('date_time', [
                Carbon::parse($filters->fromDateOfService)->startOfDay(),
                Carbon::parse($filters->toDateOfService)->endOfDay(),
            ]))
            ->when($search, fn ($q) => $q->where(function ($sq) use ($search) {
                $sq->whereLike('client', $search)
                    ->orWhereLike('address', $search)
                    ->orWhereLike('contact_no', $search)
                    ->orWhereLike('contact_person', $search)
                    ->orWhereHas('creator', fn ($subQuery) => $subQuery->whereLike('first_name', $search)
                        ->orWhereLike('middle_name', $search)
                        ->orWhereLike('last_name', $search)
                        ->orWhereLike('suffix', $search));
            }))
            ->with(['creator', 'serviceable'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->toResourceCollection();

        return Inertia::render('itservices/page/Index', compact('jobOrders'));
    }

    public function store(StoreITServiceRequest $request): RedirectResponse
    {
        $validated = $request->safe()->merge([
            'created_by' => $request->user()->employee_id,
        ]);

        DB::transaction(function () use ($validated) {
            $itService = ITService::create([
                'technician_id'   => $validated->technician_id,
                'machine_type'    => $validated->machine_type,
                'model'           => $validated->model,
                'serial_no'       => $validated->serial_no,
                'tag_no'          => $validated->tag_no,
                'machine_problem' => $validated?->machine_problem,
            ]);

            $jobOrder = new JobOrder([
                'client'         => $validated->client,
                'address'        => $validated->address,
                'department'     => $validated->department,
                'contact_no'     => $validated->contact_no,
                'contact_person' => $validated->contact_person,
                'date_time'      => $validated->date_time,
                'status'         => JobOrderStatus::ForCheckup,
                'created_by'     => $validated->created_by,
            ]);

            $jobOrder->serviceable()->associate($itService);
            $jobOrder->save();
        });

        return redirect()->route('job_order.it_service.index');
    }

    public function edit(JobOrder $jobOrder): Response
    {
        $data = $jobOrder->load([
            'creator'     => ['account'],
            'serviceable' => [
                'technician',
                'initialOnsiteReport',
                'finalOnsiteReport',
            ],
        ])->toResource();
        // dd($data);
        $regulars = Employee::query()
            ->with('account.roles')
            ->whereHas('account', fn (Builder $query) => $query->role(UserRole::Regular))
            ->get()
            ->toResourceCollection();

        return Inertia::render('itservices/page/Edit', compact('data', 'regulars'));
    }
}
