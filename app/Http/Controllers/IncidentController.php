<?php

namespace App\Http\Controllers;

use App\Enums\IncidentStatus;
use App\Models\Employee;
use App\Models\Incident;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class IncidentController extends Controller
{
public function index(Request $request)
{
    $query = Incident::with([
        'jobOrder.serviceable',
        'creator',
        'involvedEmployees',
        'hauling.form3.form4.jobOrder.serviceable',
        'hauling.haulers' 
    ])->orderBy('occured_at', 'desc');

    if ($request->has('search') && $request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('subject', 'like', '%' . $request->search . '%')
              ->orWhere('location', 'like', '%' . $request->search . '%')
              ->orWhere('infraction_type', 'like', '%' . $request->search . '%')
              ->orWhereHas('involvedEmployees', function ($q) use ($request) {
                  $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');
              });
        });
    }

    if ($request->has('statuses') && !empty($request->statuses)) {
        $query->whereIn('status', $request->statuses);
    }

    if ($request->has('dateFrom') && $request->dateFrom) {
        $query->whereDate('occured_at', '>=', $request->dateFrom);
    }

    if ($request->has('dateTo') && $request->dateTo) {
        $query->whereDate('occured_at', '<=', $request->dateTo);
    }

    if ($request->has('tab') && $request->tab !== 'All') {
        $query->whereHas('jobOrder', function ($q) use ($request) {
            if ($request->tab === 'Waste Management') {
                $q->where('serviceable_type', 'form4');
            } elseif ($request->tab === 'IT Services') {
                $q->where('serviceable_type', 'it_service');
            } elseif ($request->tab === 'Other Services') {
                $q->where('serviceable_type', 'form5');
            }
        });
    }

    $incidents = $query->get()->map(function ($incident) {
        $jobOrder = $incident->jobOrder;
        $haulingJobOrder = null;
        $haulers = [];
        
        if (!$jobOrder && $incident->hauling) {
            $haulingJobOrder = $incident->hauling->form3->form4->jobOrder ?? null;
            
            // Get haulers from hauling
            if ($incident->hauling->haulers) {
                $haulers = $incident->hauling->haulers->map(function ($hauler) {
                    return [
                        'id' => $hauler->id,
                        'name' => $hauler->first_name . ' ' . $hauler->last_name
                    ];
                })->toArray();
            }
        }
        
        $serviceableType = $jobOrder ? $jobOrder->serviceable_type : 
                          ($haulingJobOrder ? $haulingJobOrder->serviceable_type : null);
        
        $serviceType = $jobOrder ? ($jobOrder->serviceable->name ?? 'N/A') :
                     ($haulingJobOrder ? ($haulingJobOrder->serviceable->name ?? 'N/A') : 'N/A');
        
        $incidentCreator = $incident->creator ?? null;

        return [
            'id'        => $incident->id,
            'job_order' => $jobOrder ? [
                'id'               => $jobOrder->id,
                'serviceable_type' => $serviceableType,
                'service_type'     => $serviceType,
                'status'           => $jobOrder->status->value,
            ] : null,
            'hauling_job_order' => $haulingJobOrder ? [
                'id'               => $haulingJobOrder->id,
                'serviceable_type' => $haulingJobOrder->serviceable_type,
                'service_type'     => $serviceType,
            ] : null,
            'haulers' => $haulers,
            'subject' => $incident->subject,
            'location'        => $incident->location,
            'infraction_type' => $incident->infraction_type,
            'occured_at'      => $incident->occured_at->toIso8601String(),
            'description'     => $incident->description,
            'is_read'         => $incident->is_read,
            'status'          => $incident->status->value,
            'created_by'      => $incidentCreator ? [
                'id'    => $incidentCreator->id,
                'name'  => $incidentCreator->first_name.' '.$incidentCreator->last_name,
                'email' => $incidentCreator->email,
            ] : null,
            'plainText'          => $this->htmlToPlainText($incident->description),
            'involved_employees' => $incident->involvedEmployees->map(function ($employee) {
                return [
                    'id'   => $employee->id,
                    'name' => $employee->first_name.' '.$employee->last_name,
                ];
            }),
            'hauling' => $incident->hauling ? [
                'id' => $incident->hauling->id,
                'form3' => $incident->hauling->form3 ? [
                    'id' => $incident->hauling->form3->id,
                    'form4' => $incident->hauling->form3->form4 ? [
                        'id' => $incident->hauling->form3->form4->id,
                        'job_order_id' => $incident->hauling->form3->form4->jobOrder->id ?? null
                    ] : null
                ] : null
            ] : null
        ];
    });
    
    $employees = Employee::select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"))
        ->orderBy('first_name')
        ->get()
        ->toArray();

    $jobOrders = JobOrder::with('serviceable')
        ->where('status', 'active')
        ->get()
        ->map(function ($jobOrder) {
            return [
                'id' => $jobOrder->id,
                'label' => 'JO-' . $jobOrder->id . ' - ' . ($jobOrder->serviceable->name ?? 'N/A'),
                'service_type' => class_basename($jobOrder->serviceable_type),
            ];
        })
        ->toArray();

    return Inertia::render('incident-report/index', [
        'incidents' => $incidents,
        'employees' => $employees,
        'jobOrders' => $jobOrders,
        'filters' => $request->only(['search', 'statuses', 'dateFrom', 'dateTo', 'tab']),
        'auth' => [
            'user' => $request->user()->load('employee.position'),
        ],
    ]);
}

    public function archive(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:incidents,id',
        ]);

        Incident::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', 'Selected incidents archived successfully');
    }

    protected function htmlToPlainText($html)
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);

        return Str::limit(trim($text), 200);
    }

    public function markAsRead(Incident $incident)
    {
        $incident->markAsRead();

        return redirect()->route('incidents.index')->with('success', 'Incident marked as read successfully');
    }

    public function markNoIncident(JobOrder $jobOrder)
{
    $existingDraft = Incident::where('job_order_id', $jobOrder->id)
        ->where('status', 'draft')
        ->first();

    if ($existingDraft) {
        $existingDraft->update([
            'status' => 'no_incident',
            'completed_at' => now()
        ]);
    } else {
        Incident::create([
            'job_order_id' => $jobOrder->id,
            'subject' => 'No Incident Reported',
            'status' => 'no_incident',
            'completed_at' => now()
        ]);
    }

    return redirect()->back()->with('success', 'Marked as no incident for this job order.');
}

    public function verify(Incident $incident)
    {
        $incident->update(['status' => 'verified']);

        return redirect()->back()->with('success', 'Incident verified successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_order_id'         => ['required', 'exists:job_orders,id'],
            'subject'              => ['required', 'string', 'max:255'],
            'location'             => ['required', 'string', 'max:255'],
            'infraction_type'      => ['required', 'string'],
            'occured_at'           => ['required', 'date'],
            'description'          => ['required', 'string'],
            'involved_employees'   => ['nullable', 'array'],
            'involved_employees.*' => ['exists:employees,id'],
        ]);

        $user = auth()->user();

        if (!$user || !$user->employee_id) {
            return back()->with('error', 'Authenticated user has no associated employee');
        }

        DB::transaction(function () use ($validated, $user) {
            $incident = Incident::create([
                'job_order_id'    => $validated['job_order_id'],
                'created_by'      => $user->employee_id,
                'subject'         => $validated['subject'],
                'location'        => $validated['location'],
                'infraction_type' => $validated['infraction_type'],
                'occured_at'      => $validated['occured_at'],
                'description'     => $validated['description'],
                'status'          => IncidentStatus::ForVerification,
            ]);

            if (!empty($validated['involved_employees'])) {
                $incident->involvedEmployees()->sync($validated['involved_employees']);
            }
        });

        return redirect()->route('incidents.index')->with('success', 'Incident created successfully');
    }
     public function update(Request $request, Incident $incident)
    {
        if ($incident->status !== IncidentStatus::Draft) {
            return back()->with('error', 'Only draft incidents can be updated');
        }

        $validated = $request->validate([
            'job_order_id'         => ['required', 'exists:job_orders,id'],
            'subject'              => ['required', 'string', 'max:255'],
            'location'             => ['required', 'string', 'max:255'],
            'infraction_type'      => ['required', 'string'],
            'occured_at'           => ['required', 'date'],
            'description'          => ['required', 'string'],
            'involved_employees'   => ['nullable', 'array'],
            'involved_employees.*' => ['exists:employees,id'],
        ]);

        DB::transaction(function () use ($validated, $incident) {
            $incident->update([
                'job_order_id'    => $validated['job_order_id'],
                'subject'         => $validated['subject'],
                'location'        => $validated['location'],
                'infraction_type' => $validated['infraction_type'],
                'occured_at'      => $validated['occured_at'],
                'description'     => $validated['description'],
                'status'          => IncidentStatus::ForVerification, 
            ]);

            if (!empty($validated['involved_employees'])) {
                $incident->involvedEmployees()->sync($validated['involved_employees']);
            } else {
                $incident->involvedEmployees()->detach();
            }
        });

        return redirect()->route('incidents.index')->with('success', 'Incident updated successfully');
    }

}