<?php
namespace App\Http\Controllers;

use App\Enums\IncidentStatus;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Http\Requests\ArchiveIncidentsRequest;
use App\Models\Incident;
use App\Services\IncidentService;
use Inertia\Inertia;
use Illuminate\Http\Request;


class IncidentController extends Controller
{
    protected $incidentService;

    public function __construct(IncidentService $incidentService)
    {
        $this->incidentService = $incidentService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $incidents = $this->incidentService->getFilteredIncidents(
            $request->only(['search', 'statuses', 'dateFrom', 'dateTo']),
            $user
        );

        return Inertia::render('incident-report/index', [
            'incidents' => $incidents,
            'filters' => $request->only(['search', 'statuses', 'dateFrom', 'dateTo', 'tab']),
            'auth' => [
                'user' => $request->user()->load('employee.position'),
            ],
        ]);
    }

    public function archive(ArchiveIncidentsRequest $request)
    {
        Incident::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', 'Selected incidents archived successfully');
    }

    public function markAsRead(Incident $incident)
    {
        $incident->markAsRead();

        return redirect()->route('incidents.index')->with('success', 'Incident marked as read successfully');
    }

    public function markNoIncident(Incident $incident)
    {
        if ($incident->status !== IncidentStatus::Draft) { 
            return redirect()->back()->with('error', 'Only draft incidents can be marked as no incident.');
        }

        $incident->update([
            'status' => 'no incident',
            'subject' => 'No Incident Reported',
            'description' => 'This incident has been reviewed and marked as no incident.',
            'completed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Marked as no incident.');
    }

    public function verify(Incident $incident)
    {
        $incident->update(['status' => 'verified']);

        return redirect()->back()->with('success', 'Incident verified successfully');
    }

    public function store(StoreIncidentRequest $request)
    {
        $user = auth()->user();

        if (!$user || !$user->employee_id) {
            return back()->with('error', 'Authenticated user has no associated employee');
        }

        $this->incidentService->createIncident($request->validated(), $user);

        return redirect()->route('incidents.index')->with('success', 'Incident created successfully');
    }

    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        if ($incident->status !== IncidentStatus::Draft) {
            return back()->with('error', 'Only draft incidents can be updated');
        }

        $this->incidentService->updateIncident($incident, $request->validated());

        return redirect()->route('incidents.index')->with('success', 'Incident updated successfully');
    }
}