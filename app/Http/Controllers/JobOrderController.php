<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Http\Requests\UpdateJobOrderRequest;
use App\Models\JobOrder;
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

    public function update(UpdateJobOrderRequest $request, JobOrder $jobOrder)
    {
        $jobOrder->update([
            'status' => $request->enum('status', JobOrderStatus::class),
        ]);

        return redirect()->back();
    }

    public function destroy(Request $request, ?JobOrder $jobOrder = null)
    {
        $message = '';

        if ($jobOrder) {
            $jobOrder->delete();

            // return a msg
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
