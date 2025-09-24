<?php

namespace App\Http\Controllers;

use App\Enums\JobOrderStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreForm5Request;
use App\Http\Requests\UpdateForm5Request;
use App\Models\Employee;
use App\Models\Form5;
use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Form5Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class Form5Controller extends Controller
{
    public function __construct(private Form5Service $service) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $search  = $request->input('search', '');
        $filters = $request->input('filters', []);

        $data = $this->service->getAllForm5JobOrders($perPage, $search, $filters);

        return Inertia::render('job-orders/other-services/Index', [
            'data'           => $data,
            'emptySearchImg' => asset('state/search-empty.svg'),
            'emptyJobOrders' => asset('state/task-empty.svg'),
        ]);
    }

    public function store(StoreForm5Request $request): RedirectResponse
    {
        $validated = $request->safe()->merge([
            'created_by' => $request->user()->employee_id,
        ])->toArray();

        $data = $this->service->storeForm5($validated);

        [$title, $description] = explode('|', __('responses.job_order.create', [
            'ticket' => $data->ticket,
        ]));

        return redirect()->route('job_order.other_services.edit', [
            'ticket' => $data->ticket,
        ])->with(['message' => compact('title', 'description')]);
    }

    public function edit(JobOrder $ticket): Response
    {
        $data      = $this->service->getForm5Data($ticket);
        $employees = Employee::query()
            ->with('account.roles')
            ->whereHas('account', fn(Builder $query) => $query->role(UserRole::Regular))
            ->get()
            ->toResourceCollection();
        return Inertia::render('job-orders/other-services/Edit', [
            'data'      => $data,
            'employees' => $employees,
        ]);
    }

    public function update(UpdateForm5Request $request, Form5 $form5): RedirectResponse
    {
        $validated = $request->validated();

        $response = $this->service->updateForm5($validated, $form5);

        $message = __('responses.update_success');

        return back()->with(compact('message'));
    }

    public function markAsCompleted(Form5 $form5): RedirectResponse
    {
        $response = $this->service->markAsCompleted($form5);

        $message = __('responses.status_update', ['status' => JobOrderStatus::Completed->value]);

        return back()->with(compact('message'));
    }

    public function markAsInProgress(Form5 $form5): RedirectResponse
    {
        $response = $this->service->markAsInProgress($form5);

        $message = __('responses.status_update', ['status' => JobOrderStatus::InProgress->value]);

        return back()->with(compact('message'));
    }
}
