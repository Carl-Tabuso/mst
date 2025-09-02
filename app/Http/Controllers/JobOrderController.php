<?php

namespace App\Http\Controllers;

use App\Enums\ActivityLogName;
use App\Enums\JobOrderStatus;
use App\Http\Requests\UpdateJobOrderRequest;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Models\Position;
use App\Services\JobOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class JobOrderController extends Controller
{
    private const PER_PAGE = 10;

    public function __construct(private JobOrderService $service) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', self::PER_PAGE);

        $search = $request->input('search', '');

        $filters = $request->input('filters', []);

        $data = $this->service->getAllJobOrders($perPage, $search, $filters);

        return Inertia::render('job-orders/Index', [
            'data'           => $data,
            'emptySearchImg' => asset('state/search-empty.svg'),
            'emptyJobOrders' => asset('state/task-empty.svg'),
        ]);
    }

    public function create(): Response
    {
        $technicians = Employee::query()
            ->with('account')
            ->where('position_id', Position::firstWhere(['name' => 'Technician'])->id)
            ->get()
            ->toResourceCollection();

        // dd($technicians);

        return Inertia::render('job-orders/Create', compact('technicians'));
    }

    public function update(UpdateJobOrderRequest $request, JobOrder $jobOrder): RedirectResponse
    {
        $status = $request->safe()->enum('status', JobOrderStatus::class);

        $jobOrder->update(['status' => $status]);

        return back()->with([
            'message' => __('responses.status_update', ['status' => $status->value]),
        ]);
    }

    public function destroy(JobOrder $jobOrder): RedirectResponse
    {
        $jobOrder->delete();

        return redirect()->route('job_order.index')
            ->with(['message' => __('responses.archive.ticket', [
                'ticket' => $jobOrder->ticket,
            ])]);
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $jobOrderIds = $request->array('jobOrderIds');

        activity()->withoutLogs(fn () => DB::transaction(fn () => JobOrder::destroy($jobOrderIds)));

        $user = $request->user();

        activity()
            ->useLog(ActivityLogName::TicketBatchArchive->value)
            ->causedBy($user)
            ->event('deleted')
            ->withProperties([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log(__('activity.job_order.archived.batch', [
                'causer'       => $user->employee->full_name,
                'ticket_count' => count($jobOrderIds),
            ]));

        return back()->with(['message' => __('responses.batch_archive.ticket')]);
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
