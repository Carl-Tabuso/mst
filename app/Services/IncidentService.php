<?php

namespace App\Services;

use App\Enums\IncidentStatus;
use App\Enums\UserRole;
use App\Models\Incident;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncidentService
{
    public function getFilteredIncidents($filters, $user)
    {
        $employeeId = $user->employee->id ?? null;
        $isSafetyOfficer = $user->hasRole(UserRole::SafetyOfficer);
        $isTeamLeader = $user->hasRole(UserRole::TeamLeader);
        $isHumanResource = $user->hasRole(UserRole::HumanResource);
        $isCreatingRole = $isSafetyOfficer || $isTeamLeader;
        $isVerifyingRole = $isHumanResource;

        $query = Incident::with([
            'jobOrder',
            'creator',
            'hauling.form3.form4.jobOrder',
            'hauling.haulers',
            'hauling.assignedPersonnel.teamLeader',
            'hauling.assignedPersonnel.teamDriver',
            'hauling.assignedPersonnel.safetyOfficer',
            'hauling.assignedPersonnel.teamMechanic'
        ])->orderBy('occured_at', 'desc');

        // Apply role-based filters
        if (!$user->hasRole('admin')) {
            if ($isCreatingRole && $employeeId) {
                $query->where(function ($q) use ($employeeId) {
                    $q->whereHas('hauling.assignedPersonnel', function ($subQuery) use ($employeeId) {
                        $subQuery->where('team_leader', $employeeId)
                                 ->orWhere('team_driver', $employeeId)
                                 ->orWhere('safety_officer', $employeeId)
                                 ->orWhere('team_mechanic', $employeeId);
                    });
                });
            } elseif ($isVerifyingRole) {
                $query->whereNotIn('status', [IncidentStatus::Draft]);
            } elseif ($employeeId) {
                $query->where(function ($q) use ($employeeId) {
                    $q->whereHas('hauling.assignedPersonnel', function ($subQuery) use ($employeeId) {
                        $subQuery->where('team_leader', $employeeId)
                                 ->orWhere('team_driver', $employeeId)
                                 ->orWhere('safety_officer', $employeeId)
                                 ->orWhere('team_mechanic', $employeeId);
                    });
                });
            }
        }

        if (isset($filters['search']) && $filters['search']) {
            $this->applySearchFilter($query, $filters['search']);
        }

        if (isset($filters['statuses']) && !empty($filters['statuses'])) {
            $this->applyStatusFilter($query, $filters['statuses'], $isVerifyingRole, $isCreatingRole, $user);
        }

        if (isset($filters['dateFrom']) && $filters['dateFrom']) {
            $query->whereDate('occured_at', '>=', $filters['dateFrom']);
        }

        if (isset($filters['dateTo']) && $filters['dateTo']) {
            $query->whereDate('occured_at', '<=', $filters['dateTo']);
        }

        return $query->get()->map([$this, 'formatIncidentData']);
    }

    protected function applySearchFilter($query, $searchTerm)
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('subject', 'like', '%' . $searchTerm . '%')
              ->orWhere('location', 'like', '%' . $searchTerm . '%')
              ->orWhere('infraction_type', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('hauling.assignedPersonnel.teamLeader', function ($q) use ($searchTerm) {
                  $q->where('first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
              })
              ->orWhereHas('hauling.assignedPersonnel.teamDriver', function ($q) use ($searchTerm) {
                  $q->where('first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
              })
              ->orWhereHas('hauling.assignedPersonnel.safetyOfficer', function ($q) use ($searchTerm) {
                  $q->where('first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
              })
              ->orWhereHas('hauling.assignedPersonnel.teamMechanic', function ($q) use ($searchTerm) {
                  $q->where('first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
              });
        });
    }

    protected function applyStatusFilter($query, $statuses, $isVerifyingRole, $isCreatingRole, $user)
    {
        if (($isVerifyingRole || $isCreatingRole) && !$user->hasRole('admin')) {
            $filteredStatuses = array_filter($statuses, function ($status) {
                return $status !== IncidentStatus::Draft->value;
            });
            
            if (!empty($filteredStatuses)) {
                $query->whereIn('status', $filteredStatuses);
            }
        } else {
            $query->whereIn('status', $statuses);
        }
    }

    public function formatIncidentData($incident)
    {
        $jobOrder = $incident->jobOrder;
        $haulingJobOrder = null;
        $haulers = [];
        
        if (!$jobOrder && $incident->hauling) {
            $haulingJobOrder = $incident->hauling->form3->form4->jobOrder ?? null;
            
            if ($incident->hauling->haulers) {
                $haulers = $incident->hauling->haulers->map(function ($hauler) {
                    return [
                        'id' => $hauler->id,
                        'name' => $hauler->first_name . ' ' . $hauler->last_name
                    ];
                })->toArray();
            }
        }
        
        $incidentCreator = $incident->creator ?? null;

        $assignedPersonnel = [];
        if ($incident->hauling && $incident->hauling->assignedPersonnel) {
            $ap = $incident->hauling->assignedPersonnel;
            $assignedPersonnel = [
                'team_leader' => $ap->teamLeader ? [
                    'id' => $ap->teamLeader->id,
                    'name' => $ap->teamLeader->first_name . ' ' . $ap->teamLeader->last_name
                ] : null,
                'team_driver' => $ap->teamDriver ? [
                    'id' => $ap->teamDriver->id,
                    'name' => $ap->teamDriver->first_name . ' ' . $ap->teamDriver->last_name
                ] : null,
                'safety_officer' => $ap->safetyOfficer ? [
                    'id' => $ap->safetyOfficer->id,
                    'name' => $ap->safetyOfficer->first_name . ' ' . $ap->safetyOfficer->last_name
                ] : null,
                'team_mechanic' => $ap->teamMechanic ? [
                    'id' => $ap->teamMechanic->id,
                    'name' => $ap->teamMechanic->first_name . ' ' . $ap->teamMechanic->last_name
                ] : null,
            ];
        }

        return [
            'id'        => $incident->id,
            'job_order' => $jobOrder ? [
                'id'   => $jobOrder->id,
                'status' => $jobOrder->status->value,
            ] : null,
            'hauling_job_order' => $haulingJobOrder ? [
                'id' => $haulingJobOrder->id,
            ] : null,
            'haulers' => $haulers,
            'assigned_personnel' => $assignedPersonnel,
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
    }

    public function createIncident(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $incident = Incident::create([
                'created_by'      => $user->employee_id,
                'subject'         => $data['subject'],
                'location'        => $data['location'],
                'infraction_type' => $data['infraction_type'],
                'occured_at'      => $data['occured_at'],
                'description'     => $data['description'],
                'status'          => IncidentStatus::ForVerification,
            ]);

         

            return $incident;
        });
    }

    public function updateIncident(Incident $incident, array $data)
    {
        return DB::transaction(function () use ($incident, $data) {
            $incident->update([
                'subject'         => $data['subject'],
                'location'        => $data['location'],
                'infraction_type' => $data['infraction_type'],
                'occured_at'      => $data['occured_at'],
                'description'     => $data['description'],
                'status'          => IncidentStatus::ForVerification,
            ]);

            return $incident;
        });
    }

    public function htmlToPlainText($html)
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);

        return Str::limit(trim($text), 200);
    }
}