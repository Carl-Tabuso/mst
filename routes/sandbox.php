<?php

use App\Enums\UserRole;
use App\Mail\TestMail;
use App\Models\Form3AssignedPersonnel;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// for sandboxing/testing routes, do them here  - Carl

Route::get('test', function () {
    $dispatchers = User::role(UserRole::Dispatcher)->get()->pluck('email')->toArray();
    $teamLeaders = User::role(UserRole::TeamLeader)->get()->pluck('email')->toArray();
    $safetyOfficers = User::role(UserRole::SafetyOfficer)->get()->pluck('email')->toArray();
    // $head        = User::role(UserRole::HeadFrontliner)->first()->email;
    $itAdmins    = User::role(UserRole::ITAdmin)->get()->pluck('email')->toArray();
    $consultants = User::role(UserRole::Consultant)->get()->pluck('email')->toArray();
    $hrs         = User::role(UserRole::HumanResource)->get()->pluck('email')->toArray();
    $regulars    = User::role(UserRole::Regular)->get()->pluck('email')->toArray();
    $frontliners = User::role(UserRole::Frontliner)->get()->pluck('email')->toArray();

    dd([
        'dispatchers'     => $dispatchers,
        'team leaders'    => $teamLeaders,
        'safety officers' => $safetyOfficers,
        // 'head'            => $head,
        'it admins'       => $itAdmins,
        'consultants'     => $consultants,
        'human resources' => $hrs,
        'regulars'        => $regulars,
        'frontliners'     => $frontliners,
    ]);
});
Route::get('test-incident-access', function () {
    $draftIncidents = \App\Models\Incident::with([
        'hauling.assignedPersonnel.teamLeader.account',
        'hauling.assignedPersonnel.teamDriver.account',
        'hauling.assignedPersonnel.safetyOfficer.account',
        'hauling.assignedPersonnel.teamMechanic.account'
    ])
    ->where('status', \App\Enums\IncidentStatus::Draft)
    ->limit(5)
    ->get()
    ->map(function ($incident) {
        $personnel = [];
        if ($incident->hauling && $incident->hauling->assignedPersonnel) {
            $ap = $incident->hauling->assignedPersonnel;
            $personnel = [
                'team_leader' => $ap->teamLeader ? $ap->teamLeader->account->email ?? 'No user account' : null,
                'team_driver' => $ap->teamDriver ? $ap->teamDriver->account->email ?? 'No user account' : null,
                'safety_officer' => $ap->safetyOfficer ? $ap->safetyOfficer->account->email ?? 'No user account' : null,
                'team_mechanic' => $ap->teamMechanic ? $ap->teamMechanic->account->email ?? 'No user account' : null,
            ];
        }

        return [
            'incident_id' => $incident->id,
            'subject' => $incident->subject,
            'status' => $incident->status->value,
            'assigned_personnel_emails' => array_filter($personnel) 
        ];
    });

    $roles = [
        'dispatchers' => User::role(UserRole::Dispatcher)->get()->pluck('email')->toArray(),
        'team_leaders' => User::role(UserRole::TeamLeader)->get()->pluck('email')->toArray(),
        'safety_officers' => User::role(UserRole::SafetyOfficer)->get()->pluck('email')->toArray(),
        'it_admins' => User::role(UserRole::ITAdmin)->get()->pluck('email')->toArray(),
        'consultants' => User::role(UserRole::Consultant)->get()->pluck('email')->toArray(),
        'human_resources' => User::role(UserRole::HumanResource)->get()->pluck('email')->toArray(),
        'regulars' => User::role(UserRole::Regular)->get()->pluck('email')->toArray(),
        'frontliners' => User::role(UserRole::Frontliner)->get()->pluck('email')->toArray(),
    ];

    dd([
        'draft_incidents' => $draftIncidents,
        'users_by_role' => $roles,
        'note' => 'Safety officers cannot see these draft incidents. Use the emails above to test login.'
    ]);
});
Route::get('test-mail', function () {
    return new TestMail;
});
