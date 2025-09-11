<?php

namespace App\Http\Controllers;

use App\Enums\ActivityLogName;
use App\Models\JobOrder;
use App\Services\JobOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArchivedJobOrderController extends Controller
{
    public function __construct(private JobOrderService $service) {}

    public function index(Request $request): Response
    {
        $perPage = $request->integer('per_page', 10);

        $search = $request->input('search', '');

        $filters = $request->input('filters', []);

        $data = $this->service->getAllJobOrders($perPage, $search, $filters, true);

        return Inertia::render('archives/job-orders/Index', compact('data'));
    }

    public function update(JobOrder $jobOrder): RedirectResponse
    {
        $this->service->restoreArchivedJobOrder($jobOrder);

        $message = __('responses.restore.ticket', ['ticket' => $jobOrder->ticket]);

        return back()->with(compact('message'));
    }

    public function bulkRestore(Request $request): RedirectResponse
    {
        $archivedJobOrderIds = $request->input('jobOrderIds', []);

        $this->service->restoreArchivedJobOrders($archivedJobOrderIds);

        $user = $request->user();

        $archivedJobOrderIdCount = count($archivedJobOrderIds);

        activity()
            ->useLog(ActivityLogName::TicketBatchArchive->value)
            ->causedBy($user)
            ->event('deleted')
            ->withProperties([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log(__('activity.job_order.restored.batch', [
                'causer'       => $user->employee->full_name,
                'ticket_count' => $archivedJobOrderIdCount,
            ]));

        $message = __('responses.batch_restore.ticket', ['count' => $archivedJobOrderIdCount]);

        return back()->with(compact('message'));
    }

    public function destroy(JobOrder $jobOrder): RedirectResponse
    {
        $this->service->permanentlyDeleteJobOrder($jobOrder);

        $message = __('responses.permanent_delete.ticket', ['ticket' => $jobOrder->ticket]);

        return back()->with(compact('message'));
    }
}
