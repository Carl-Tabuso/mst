<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Enums\IncidentStatus;
use Inertia\Inertia;

class IncidentController extends Controller
{

    public function showReport()
{
    return Inertia::render('incident-report/index', [
      
    ]);
}
    public function index()
    {
        $incidents = Incident::with([
            'jobOrder.serviceable',
            'creator',
            'involvedEmployees'
        ])
        ->orderBy('occured_at', 'desc')
        ->get()
        ->map(function ($incident) {
            $jobOrder = $incident->jobOrder;
            $serviceableType = $jobOrder->serviceable_type ?? null;
            $incidentCreator = $incident->creator ?? null;
            
            return [
                'id' => $incident->id,
 'job_order' => $jobOrder ? [
                    'id' => $jobOrder->id,
                    'serviceable_type' => $serviceableType,
                    'status' => $jobOrder->status->value,
                  
                ] : null,                'subject' => $incident->subject,
                'location' => $incident->location,
                'infraction_type' => $incident->infraction_type,
                'occured_at' => $incident->occured_at->toIso8601String(),
                'description' => $incident->description,
                'is_read' => $incident->is_read,
                'status' => $incident->status->value,
                'created_by' => $incidentCreator ? [
                    'id' => $incidentCreator->id,
                    'name' => $incidentCreator->first_name . ' ' . $incidentCreator->last_name,
                    'email' => $incidentCreator->email
                ] : null,
                'plainText' => $this->htmlToPlainText($incident->description),
                'involved_employees' => $incident->involvedEmployees->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->first_name . ' ' . $employee->last_name
                    ];
                })
            ];
        });

        return response()->json($incidents);
    }

public function archive(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:incidents,id'
    ]);

    Incident::whereIn('id', $request->ids)->delete();

    return response()->json([
        'message' => 'Selected incidents archived successfully'
    ]);
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
    return response()->json(['success' => true]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_order_id' => ['required', 'exists:job_orders,id'],
            'subject' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'infraction_type' => ['required', 'string'],
            'occured_at' => ['required', 'date'],
            'description' => ['required', 'string'],
            'involved_employees' => ['nullable', 'array'],
            'involved_employees.*' => ['exists:employees,id'],
        ]);

        $user = auth()->user();
        
        if (!$user || !$user->employee_id) {
            return response()->json(['message' => 'Authenticated user has no associated employee'], 403);
        }

        DB::transaction(function () use ($validated, $user) {
            $incident = Incident::create([
                'job_order_id' => $validated['job_order_id'],
                'created_by' => $user->employee_id,
                'subject' => $validated['subject'],
                'location' => $validated['location'],
                'infraction_type' => $validated['infraction_type'],
                'occured_at' => $validated['occured_at'],
                'description' => $validated['description'],
                'status' => IncidentStatus::ForVerification,
            ]);

            if (!empty($validated['involved_employees'])) {
                $incident->involvedEmployees()->sync($validated['involved_employees']);
            }
        });

        return response()->json(['message' => 'Incident created successfully'], 201);
    }
}