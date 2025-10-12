<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeProfileController extends Controller
{
    public function show($id)
    {
        $employee = Employee::with([
            'position',
            'account',
            'createdJobOrders'                                                => function ($query) {
                $query->with([
                    'serviceable' => function ($morphTo) {
                        $morphTo->morphWith([
                            \App\Models\Form4::class => [
                                'form3.haulings.assignedPersonnel.teamLeader',
                                'form3.haulings.assignedPersonnel.safetyOfficer',
                                'form3.haulings.assignedPersonnel.teamMechanic',
                                'form3.haulings.drivers',
                            ],
                        ]);
                    },
                ]);
            },
            'form3sHauler.form3.form4.jobOrder'                               => function ($query) {
                $query->with([
                    'serviceable' => function ($morphTo) {
                        $morphTo->morphWith([
                            \App\Models\Form4::class => [
                                'form3.haulings.assignedPersonnel.teamLeader',
                                'form3.haulings.assignedPersonnel.safetyOfficer',
                                'form3.haulings.assignedPersonnel.teamMechanic',
                                'form3.haulings.drivers',
                            ],
                        ]);
                    },
                ]);
            },
            'form3sHauler.assignedPersonnel.teamLeader',
            'form3sHauler.assignedPersonnel.safetyOfficer',
            'form3sHauler.assignedPersonnel.teamMechanic',
            'form3sHauler.drivers',

            'assignedPersonnelAsTeamLeader.form3Hauling.form3.form4.jobOrder' => function ($query) {
                $query->with([
                    'serviceable' => function ($morphTo) {
                        $morphTo->morphWith([
                            \App\Models\Form4::class => [
                                'form3.haulings.assignedPersonnel.teamLeader',
                                'form3.haulings.assignedPersonnel.safetyOfficer',
                                'form3.haulings.assignedPersonnel.teamMechanic',
                                'form3.haulings.drivers',
                            ],
                        ]);
                    },
                ]);
            },
            'form3sDriver.form3Hauling.form3.form4.jobOrder',
            'assignedPersonnelAsSafetyOfficer.form3Hauling.form3.form4.jobOrder',
            'assignedPersonnelAsMechanic.form3Hauling.form3.form4.jobOrder',
            'performancesAsEmployee.ratings.performanceRating',
        ])->findOrFail($id);

        $position = strtolower($employee->position->name ?? '');

        $assignedJobOrders    = $this->getAssignedJobOrdersByPosition($employee, $position);
        $jobOrderStats        = $this->generateJobOrderStats($assignedJobOrders, $position);
        $transformedJobOrders = $this->transformJobOrders($assignedJobOrders);
        $averageRating        = $this->calculateAverageRating($employee);

        $profileData = [
            'employee'                 => [
                'id'          => $employee->id,
                'full_name'   => $employee->full_name,
                'first_name'  => $employee->first_name,
                'middle_name' => $employee->middle_name,
                'last_name'   => $employee->last_name,
                'suffix'      => $employee->suffix,
                'email'       => $employee->account->email ?? null,
                'avatar'      => $employee->account && $employee->account->avatar
                    ? $employee->account->avatar
                    : null,
                'position'    => $employee->position,
            ],
            'assignedJobOrders'        => $transformedJobOrders,
            'averagePerformanceRating' => $averageRating,
            'jobOrderStats'            => $jobOrderStats,
            'position'                 => $position,
        ];

        switch ($position) {
            case 'frontliner':
                $profileData['createdJobOrders']     = $jobOrderStats;
                $profileData['createdJobOrdersList'] = $this->getDetailedCreatedJobOrders($employee);
                $profileData['performanceStats']     = $this->getFrontlinerPerformanceStats($employee);
                break;

            case 'team leader':
                $profileData['teamStats']              = $this->getTeamLeaderStats($employee);
                $profileData['performanceEvaluations'] = $this->getPerformanceEvaluations($employee);
                break;

            case 'hauler':
            case 'driver':
            case 'mechanic':
            case 'safety officer':
            case 'technician':
            case 'electrician':
                $profileData['performanceEvaluations'] = $this->getPerformanceEvaluations($employee);
                break;
        }

        return inertia('EmployeeProfile/page/Profile', $profileData);
    }

    /**
     * Extract team leader and driver information from a job order
     */
    private function extractTeamLeaderInfo($jobOrder)
    {
        $teamLeaderInfo = [
            'has_serviceable'  => ! is_null($jobOrder->serviceable),
            'serviceable_type' => $jobOrder->serviceable_type,
            'team_leader'      => null,
            'safety_officer'   => null,
            'drivers'          => [],
            'haulings_count'   => 0,
            'debug_path'       => [],
        ];

        $isForm4 = false;
        if (is_object($jobOrder->serviceable_type)) {
            $enumValue = method_exists($jobOrder->serviceable_type, 'value') ? $jobOrder->serviceable_type->value : null;
            $isForm4   = in_array($enumValue, ['form4', 'Form4', 'App\\Models\\Form4']) ||
            $jobOrder->serviceable_type instanceof \App\Enums\JobOrderServiceType;
            $teamLeaderInfo['debug_path'][] = 'Serviceable type is enum with value: ' . ($enumValue ?? 'unknown');
            $teamLeaderInfo['debug_path'][] = 'Enum class: ' . get_class($jobOrder->serviceable_type);

        } else {
            $isForm4                        = in_array($jobOrder->serviceable_type, ['form4', 'Form4', 'App\\Models\\Form4']);
            $teamLeaderInfo['debug_path'][] = 'Serviceable type is string: ' . $jobOrder->serviceable_type;
        }

        $serviceableIsForm4             = $jobOrder->serviceable instanceof \App\Models\Form4;
        $teamLeaderInfo['debug_path'][] = 'Serviceable model is Form4 instance: ' . ($serviceableIsForm4 ? 'true' : 'false');

        $isForm4 = $isForm4 || $serviceableIsForm4;

        $teamLeaderInfo['debug_path'][] = 'Is Form4 check result: ' . ($isForm4 ? 'true' : 'false');
        $teamLeaderInfo['debug_path'][] = 'Has serviceable: ' . ($jobOrder->serviceable ? 'true' : 'false');

        if ($jobOrder->serviceable) {
            $teamLeaderInfo['debug_path'][] = 'Serviceable class: ' . get_class($jobOrder->serviceable);
            $teamLeaderInfo['debug_path'][] = 'Serviceable ID: ' . $jobOrder->serviceable->id;

            $teamLeaderInfo['debug_path'][] = 'Raw serviceable_type from DB: ' . $jobOrder->getRawOriginal('serviceable_type');
            $teamLeaderInfo['debug_path'][] = 'Raw serviceable_id from DB: ' . $jobOrder->getRawOriginal('serviceable_id');
        }

        if ($isForm4 && $jobOrder->serviceable) {
            $teamLeaderInfo['debug_path'][] = 'Found Form4 serviceable';

            $form3 = $jobOrder->serviceable->form3;
            if ($form3) {
                $teamLeaderInfo['debug_path'][]   = 'Found Form3 with ID: ' . $form3->id;
                $teamLeaderInfo['haulings_count'] = $form3->haulings->count();
                $teamLeaderInfo['debug_path'][]   = 'Haulings count: ' . $teamLeaderInfo['haulings_count'];

                $haulings                        = $form3->haulings;
                $teamLeaderInfo['haulings_data'] = $haulings->map(function ($hauling, $index) {
                    $assignedPersonnel = $hauling->assignedPersonnel;
                    $drivers           = $hauling->drivers;

                    return [
                        'hauling_index'          => $index,
                        'hauling_id'             => $hauling->id,
                        'has_assigned_personnel' => ! is_null($assignedPersonnel),
                        'assigned_personnel_id'  => $assignedPersonnel?->id,
                        'team_leader_id'         => $assignedPersonnel?->team_leader,
                        'team_leader_name'       => $assignedPersonnel?->teamLeader?->full_name,
                        'safety_officer_name'    => $assignedPersonnel?->safetyOfficer?->full_name,
                        'drivers'                => $drivers->pluck('full_name')->toArray(),
                        'drivers_count'          => $drivers->count(),
                    ];
                })->toArray();

                $teamLeaderInfo['debug_path'][] = 'Haulings data extracted for ' . $haulings->count() . ' haulings';

                $firstHauling = $haulings->first();
                if ($firstHauling) {
                    $teamLeaderInfo['debug_path'][] = 'Found first hauling with ID: ' . $firstHauling->id;

                    if ($firstHauling->assignedPersonnel) {
                        $teamLeaderInfo['debug_path'][]   = 'Found assigned personnel with ID: ' . $firstHauling->assignedPersonnel->id;
                        $teamLeaderInfo['team_leader']    = $firstHauling->assignedPersonnel->teamLeader?->full_name;
                        $teamLeaderInfo['safety_officer'] = $firstHauling->assignedPersonnel->safetyOfficer?->full_name;

                        $teamLeaderInfo['debug_path'][] = 'Team Leader ID: ' . ($firstHauling->assignedPersonnel->team_leader ?? 'null');
                        $teamLeaderInfo['debug_path'][] = 'Team Leader Name: ' . ($teamLeaderInfo['team_leader'] ?? 'null');
                    } else {
                        $teamLeaderInfo['debug_path'][] = 'No assigned personnel found in first hauling';
                    }

                    $drivers = $firstHauling->drivers;
                    if ($drivers->isNotEmpty()) {
                        $teamLeaderInfo['drivers']      = $drivers->pluck('full_name')->toArray();
                        $teamLeaderInfo['debug_path'][] = 'Found ' . count($teamLeaderInfo['drivers']) . ' drivers';
                    } else {
                        $teamLeaderInfo['debug_path'][] = 'No drivers found in first hauling';
                    }
                } else {
                    $teamLeaderInfo['debug_path'][] = 'No haulings found';
                }
            } else {
                $teamLeaderInfo['debug_path'][] = 'No Form3 found for Form4 ID: ' . $jobOrder->serviceable->id;
            }
        } else {
            $teamLeaderInfo['debug_path'][] = 'Condition failed - isForm4: ' . ($isForm4 ? 'true' : 'false') . ', has serviceable: ' . ($jobOrder->serviceable ? 'true' : 'false');
        }

        return $teamLeaderInfo;
    }

    private function getDetailedCreatedJobOrders(Employee $employee)
    {
        return $employee->createdJobOrders()
            ->with([
                'serviceable' => function ($morphTo) {
                    $morphTo->morphWith([
                        \App\Models\Form4::class => [
                            'form3.haulings.assignedPersonnel.teamLeader',
                            'form3.haulings.assignedPersonnel.safetyOfficer',
                            'form3.haulings.drivers',
                        ],
                    ]);
                },
            ])
            ->latest()
            ->get()
            ->map(function ($jobOrder) {
                $teamLeaderInfo = $this->extractTeamLeaderInfo($jobOrder);

                return [
                    'id'               => $jobOrder->id,
                    'ticket'           => $jobOrder->ticket,
                    'status'           => $jobOrder->status,
                    'client'           => $jobOrder->client,
                    'serviceable_type' => $jobOrder->serviceable_type,
                    'team_leader'      => $teamLeaderInfo['team_leader'] ?? 'N/A',
                    'drivers'          => $teamLeaderInfo['drivers'] ?? [],
                    'consultant'       => 'N/A',
                    'service_area'     => $jobOrder->address ?? $jobOrder->client ?? 'N/A',
                    'created_at'       => $jobOrder->created_at,
                ];
            });
    }

    private function getTeamLeaderStats(Employee $employee)
    {

        $teamLeaderAssignments = $employee->assignedPersonnelAsTeamLeader()->count();
        $totalTeamJobOrders    = $this->pluckJobOrdersFromPersonnelAssignments($employee->assignedPersonnelAsTeamLeader)->count();

        return [
            'teams_managed'         => $teamLeaderAssignments,
            'total_team_job_orders' => $totalTeamJobOrders,
            'team_success_rate'     => $this->calculateTeamSuccessRate($employee),
        ];
    }

    private function calculateTeamSuccessRate(Employee $employee)
    {
        $teamJobs      = $this->pluckJobOrdersFromPersonnelAssignments($employee->assignedPersonnelAsTeamLeader);
        $completedJobs = $teamJobs->where('status', 'completed')->count();
        $totalJobs     = $teamJobs->count();

        return $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 2) : 0;
    }

    private function getAssignedJobOrdersByPosition(Employee $employee, string $position)
    {
        return match ($position) {
            'hauler'         => $this->pluckJobOrdersFromHauler($employee->form3sHauler),
            'team leader'    => $this->pluckJobOrdersFromPersonnelAssignments($employee->assignedPersonnelAsTeamLeader),
            'driver'         => $this->pluckJobOrdersFromDrivers($employee->form3sDriver),
            'safety officer' => $this->pluckJobOrdersFromPersonnelAssignments($employee->assignedPersonnelAsSafetyOfficer),
            'mechanic'       => $this->pluckJobOrdersFromPersonnelAssignments($employee->assignedPersonnelAsMechanic),
            'frontliner'     => $employee->createdJobOrders,
            default          => collect(),
        };
    }

    private function pluckJobOrdersFromHauler($form3Haulers)
    {
        return $form3Haulers
            ->map(fn($form3Hauler) => $form3Hauler->form3?->form4?->jobOrder)
            ->filter()
            ->unique('id')
            ->values();
    }

    private function pluckJobOrdersFromDrivers($form3Drivers)
    {
        return $form3Drivers
            ->map(fn($form3Driver) => $form3Driver->form3Hauling?->form3?->form4?->jobOrder)
            ->filter()
            ->unique('id')
            ->values();
    }

    private function pluckJobOrdersFromPersonnelAssignments($personnelAssignments)
    {
        return $personnelAssignments
            ->map(fn($assignment) => $assignment->form3Hauling?->form3?->form4?->jobOrder)
            ->filter()
            ->unique('id')
            ->values();
    }

    private function transformJobOrders($jobOrders)
    {
        $transformedOrders = $jobOrders
            ->map(function ($jobOrder) {
                if (! $jobOrder) {
                    return null;
                }

                $status = is_object($jobOrder->status) && method_exists($jobOrder->status, 'value')
                    ? $jobOrder->status->value
                    : (is_array($jobOrder->status) ? array_values($jobOrder->status)[0] : $jobOrder->status);

                $teamLeaderInfo = $this->extractTeamLeaderInfo($jobOrder);

                $transformedOrder = [
                    'id'               => $jobOrder->id,
                    'ticket'           => $jobOrder->ticket,
                    'status'           => $status,
                    'serviceable_type' => $jobOrder->serviceable_type ?? null,
                    'client'           => $jobOrder->client ?? null,
                    'team_leader'      => $teamLeaderInfo['team_leader'],
                    'safety_officer'   => $teamLeaderInfo['safety_officer'],
                    'drivers'          => $teamLeaderInfo['drivers'],
                    'service_area'     => $jobOrder->address ?? $jobOrder->client ?? null,
                    'created_at'       => $jobOrder->created_at,
                    'updated_at'       => $jobOrder->updated_at,
                ];

                return $transformedOrder;
            })
            ->filter()
            ->values();

        return $transformedOrders;
    }

    private function generateJobOrderStats($jobOrders, string $position)
    {
        if (! in_array($position, ['team leader', 'frontliner']) || $jobOrders->isEmpty()) {
            return null;
        }

        $statusCounts = $jobOrders->groupBy('status')->map->count();

        return [
            'total'     => $jobOrders->count(),
            'by_status' => $statusCounts->toArray(),
        ];
    }

    private function calculateAverageRating(Employee $employee)
    {
        $allRatings = $employee->performancesAsEmployee
            ->flatMap(fn($perf) => $perf->ratings)
            ->pluck('performanceRating.scale')
            ->filter();

        return $allRatings->count() ? round($allRatings->avg(), 2) : null;
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $user     = $employee->account;

        if (! $user) {
            return redirect()->back()->withErrors(['error' => 'User account not found']);
        }

        $validated = $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->getRawOriginal('avatar'));
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
        }

        return redirect()->back()->with('success', 'Profile photo updated successfully!');
    }

    public function getPerformanceData(Request $request, $id)
    {
        if (auth()->user()->employee_id != $id) {
            abort(403, 'Unauthorized');
        }

        $employee = Employee::findOrFail($id);

        $month = $request->get('month', now()->format('Y-m'));

        $evaluations = $this->getPerformanceEvaluations($employee, $month);

        $performanceStats = $this->getFrontlinerPerformanceStatsForMonth($employee, $month);

        return response()->json([
            'evaluations' => $evaluations,
            'stats'       => $performanceStats,
            'month'       => $month,
            'debug'       => [
                'requested_month' => $request->get('month'),
                'processed_month' => $month,
            ],
        ]);
    }

    private function getFrontlinerPerformanceStatsForMonth(Employee $employee, $month)
    {
        try {
            $startOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endOfMonth   = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        } catch (\Exception $e) {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth   = now()->endOfMonth();
            $month        = now()->format('Y-m');
        }

        $jobOrdersInMonthQuery = $employee->createdJobOrders()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        $monthlyCreated = $jobOrdersInMonthQuery->count();

        $allJobOrdersUpToMonthQuery = $employee->createdJobOrders()
            ->where('created_at', '<=', $endOfMonth);

        $totalCreated = $allJobOrdersUpToMonthQuery->count();

        $completedJobsQuery = $employee->createdJobOrders()
            ->where('created_at', '<=', $endOfMonth)
            ->where('status', \App\Enums\JobOrderStatus::Completed);

        $completedJobs = $completedJobsQuery->count();

        $jobOrdersWithCorrectionsQuery = $employee->createdJobOrders()
            ->where('created_at', '<=', $endOfMonth)
            ->whereHas('corrections');

        $jobOrdersWithCorrections = $jobOrdersWithCorrectionsQuery->count();

        $totalErrorCount = $employee->createdJobOrders()
            ->where('created_at', '<=', $endOfMonth)
            ->sum('error_count');

        $monthlyErrorCount = $employee->createdJobOrders()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('error_count');

        $successRate = $totalCreated > 0 ? round(($completedJobs / $totalCreated) * 100, 2) : 0;

        $stats = [
            'total_created'       => $totalCreated,
            'monthly_created'     => $monthlyCreated,
            'completed_jobs'      => $completedJobs,
            'corrections_count'   => $jobOrdersWithCorrections,
            'error_count'         => $totalErrorCount,
            'monthly_error_count' => $monthlyErrorCount,
            'success_rate'        => $successRate,
            'current_month'       => \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y'),
        ];

        return $stats;
    }

    private function getPerformanceEvaluations(Employee $employee, $month = null)
    {
        $query = $employee->performancesAsEmployee();

        if ($month) {
            $startOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endOfMonth   = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        }

        $evaluations = $query->latest('created_at')->get();

        return $evaluations;
    }

    private function getFrontlinerPerformanceStats(Employee $employee)
    {
        return $this->getFrontlinerPerformanceStatsForMonth($employee, now()->format('Y-m'));
    }
}
