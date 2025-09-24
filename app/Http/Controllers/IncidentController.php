<?php

namespace App\Http\Controllers;

use App\Enums\IncidentStatus;
use App\Enums\UserRole;
use App\Http\Requests\ArchiveIncidentsRequest;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Incident;
use App\Services\IncidentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
            'filters'   => $request->only(['search', 'statuses', 'dateFrom', 'dateTo', 'tab']),

        ]);
    }

    public function createSecondary($haulingId)
    {
        $user = Auth::user();

        if (! $user->hasRole(UserRole::Consultant)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $newIncident = $this->incidentService->createSecondIncidentForHauling($haulingId, $user);

            return redirect()->route('incident-report.edit', $newIncident->id)
                ->with('success', 'Secondary incident created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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
            'status'       => IncidentStatus::NoIncident,
            'subject'      => 'No Incident Reported',
            'description'  => 'This incident has been reviewed and marked as no incident.',
            'completed_at' => now(),
            'created_by'  => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Marked as no incident.');
    }

    public function verify(Incident $incident)
    {
        $incident->update(['status' => IncidentStatus::Verified]);

        return redirect()->back()->with('success', 'Incident verified successfully');
    }

    public function store(StoreIncidentRequest $request)
    {
        $user = auth()->user();

        if (! $user || ! $user->employee_id) {
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

        $this->incidentService->updateIncident($incident, $request->validated(), $request->user());

        return redirect()->route('incidents.index')->with('success', 'Incident updated successfully');
    }
}
