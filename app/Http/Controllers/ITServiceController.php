<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Enums\UserRole;
use App\Filters\JobOrder\ApplyDateOfServiceRange;
use App\Filters\JobOrder\FilterOnlyCreated;
use App\Filters\JobOrder\FilterServiceType;
use App\Filters\JobOrder\FilterStatuses;
use App\Filters\JobOrder\SearchDetails;
use App\Http\Requests\StoreITServiceRequest;
use App\Models\Employee;
use App\Models\ITService;
use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;
use Inertia\Inertia;
use Inertia\Response;

class ITServiceController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);

        $search = $request->input('search', '');

        $filters = $request->input('filters', []);

        $user = $request->user();

        $pipes = [
            new FilterServiceType(JobOrderServiceType::ITService),
            new FilterOnlyCreated($user),
            new FilterStatuses($filters),
            new ApplyDateOfServiceRange($filters),
            new SearchDetails($search),
        ];

        $jobOrders = Pipeline::send(JobOrder::query())
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage) {
                return $query->with('creator')
                    ->latest()
                    ->paginate($perPage)
                    ->withQueryString()
                    ->toResourceCollection();
            });

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
                'client'           => $validated->client,
                'address'          => $validated->address,
                'department'       => $validated->department,
                'contact_no'       => $validated->contact_no,
                'contact_position' => $validated?->contact_position,
                'contact_person'   => $validated->contact_person,
                'date_time'        => $validated->date_time,
                'status'           => JobOrderStatus::ForCheckup,
                'created_by'       => $validated->created_by,
            ]);

            $jobOrder->serviceable()->associate($itService);
            $jobOrder->save();
        });

        return redirect()->route('job_order.it_service.index');
    }

    public function edit(JobOrder $jobOrder): Response
    {
        $data = $jobOrder->load([
            'corrections',
            'creator'     => ['account'],
            'serviceable' => [
                'technician',
                'initialOnsiteReport',
                'finalOnsiteReport',
            ],
        ])->toResource();

        $regulars = Employee::query()
            ->with('account.roles')
            ->whereHas('account', fn (Builder $query) => $query->role(UserRole::Regular))
            ->get()
            ->toResourceCollection();

        return Inertia::render('itservices/page/Edit', compact('data', 'regulars'));
    }
}
