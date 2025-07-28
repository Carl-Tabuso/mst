<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\UpdateJobOrderRequest;
use App\Models\JobOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class JobOrderController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', self::PER_PAGE);

        $search = "%{$request->input('search')}%";

        $filters = null;

        if ($request->has('filters')) {
            $filters = (object) $request->array('filters');
        }

        $hasStatuses = isset($filters->statuses) && count($filters->statuses) > 0;

        $hasDateOfServiceRange =
            isset($filters->fromDateOfService, $filters->toDateOfService) &&
            ($filters->fromDateOfService && $filters->toDateOfService);

        $jobOrders = JobOrder::query()
            ->when($hasStatuses, fn ($q) => $q->ofStatuses($filters->statuses))
            ->when($hasDateOfServiceRange, function ($q) use ($filters) {
                return $q->where(function ($sq) use ($filters) {
                    return $sq->whereBetween('date_time', [
                        Carbon::parse($filters->fromDateOfService)->startOfDay(),
                        Carbon::parse($filters->toDateOfService)->endOfDay(),
                    ]);
                });
            })
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($sq) use ($search) {
                    $sq
                        ->whereAny([
                            'client',
                            'address',
                            'contact_no',
                            'contact_person',
                        ], 'like', $search)
                        ->orWhereHas('creator', function ($ssq) use ($search) {
                            return $ssq->whereAny([
                                'first_name',
                                'middle_name',
                                'last_name',
                                'suffix',
                            ], 'like', $search);
                        });
                });
            })
            ->with(['creator'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->toResourceCollection();

        return Inertia::render('job-orders/Index', compact('jobOrders'));
    }

    public function create(): Response
    {
        return Inertia::render('job-orders/Create');
    }

    public function show(JobOrder $jobOrder)
    {
        //
    }

    public function edit(JobOrder $jobOrder)
    {
        //
    }

    public function update(UpdateJobOrderRequest $request, JobOrder $jobOrder): RedirectResponse
    {
        $status = $request->safe()->enum('status', JobOrderStatus::class);

        $jobOrder->update(['status' => $status]);

        return back()->with([
            'message' => __('responses.status_update', ['status' => $status->value]),
        ]);
    }

    public function destroy(Request $request, ?JobOrder $jobOrder = null)
    {
        if ($jobOrder) {
            $jobOrder->delete();

            return redirect()->route('job_order.index')->with([
                'message' => __('responses.archive', [
                    'ticket' => $jobOrder->ticket,
                ]),
            ]);
        }

        $jobOrderIds = $request->array('jobOrderIds');

        JobOrder::destroy($jobOrderIds);

        // return a msg?
    }

    public function dropdownOptions()
    {
        return JobOrder::select('id', 'serviceable_type')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn ($j) => [
                'id'           => $j->id,
                'label'        => "{$j->id} - ".$this->getServiceTypeLabel($j->serviceable_type),
                'service_type' => $this->getServiceTypeLabel($j->serviceable_type),
            ]);
    }

    private function getServiceTypeLabel(string $type): string
    {
        return match ($type) {
            'form4'      => 'Waste Management',
            'form5'      => 'Other Services',
            'it_service' => 'IT Services',
            default      => 'Unknown',
        };
    }
}
