<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeProfileController extends Controller
{
    public function show($id)
    {
        if (auth()->user()->employee_id != $id) {
            abort(403, 'Unauthorized');
        }

        $employee = Employee::with([
            'position',
            'itServicesAsTechnician',
            'createdJobOrders',
            'form3sHauler.form4.jobOrder',
            'form3sAsTeamLeader.form4.jobOrder',
            'form3sAsDriver.form4.jobOrder',
            'form3sAsSafetyOfficer.form4.jobOrder',
            'form3sAsMechanic.form4.jobOrder',
            'performancesAsEmployee.ratings.performanceRating',
        ])->findOrFail($id);

        $position = strtolower($employee->position->name ?? '');

        $assignedJobOrders = $this->getAssignedJobOrdersByPosition($employee, $position);

        $jobOrderStats = $this->generateJobOrderStats($assignedJobOrders, $position);

        $transformedJobOrders = $this->transformJobOrders($assignedJobOrders);

        $averageRating = $this->calculateAverageRating($employee);

        $positionsWithStats = ['hauler', 'team leader', 'driver', 'mechanic', 'safety officer', 'technician', 'electrician'];
        $showProfileStats   = in_array($position, $positionsWithStats);

        return inertia('EmployeeProfile/page/Profile', [
            'employee'                 => $employee,
            'assignedJobOrders'        => $transformedJobOrders,
            'averagePerformanceRating' => $averageRating,
            'showProfileStats'         => $showProfileStats,
            'jobOrderStats'            => $jobOrderStats,
            'createdJobOrders'         => $position === 'frontliner' ? $jobOrderStats : null,
            'createdJobOrdersList'     => $position === 'frontliner' ? $transformedJobOrders : null,
        ]);
    }

    private function getAssignedJobOrdersByPosition(Employee $employee, string $position)
    {
        return match ($position) {
            'hauler'         => $this->pluckJobOrders($employee->form3sHauler),
            'team leader'    => $this->pluckJobOrders($employee->form3sAsTeamLeader),
            'driver'         => $this->pluckJobOrders($employee->form3sAsDriver),
            'safety officer' => $this->pluckJobOrders($employee->form3sAsSafetyOfficer),
            'mechanic'       => $this->pluckJobOrders($employee->form3sAsMechanic),
            'frontliner'     => $employee->createdJobOrders,
            default          => collect(),
        };
    }

    private function pluckJobOrders($form3s)
    {
        return $form3s
            ->map(fn ($form3) => $form3->form4?->jobOrder)
            ->filter()
            ->unique('id')
            ->values();
    }

    private function transformJobOrders($jobOrders)
    {
        return $jobOrders
            ->map(function ($jobOrder) {
                if (! $jobOrder) {
                    return null;
                }

                $status = is_object($jobOrder->status) && method_exists($jobOrder->status, 'value')
                ? $jobOrder->status->value
                : (is_array($jobOrder->status) ? array_values($jobOrder->status)[0] : $jobOrder->status);

                return [
                    'id'               => $jobOrder->id,
                    'status'           => $status,
                    'serviceable_type' => $jobOrder->serviceable_type ?? null,
                    'client'           => $jobOrder->client           ?? null,
                ];
            })
            ->filter()
            ->values();
    }

    private function generateJobOrderStats($jobOrders, string $position)
    {
        if (! in_array($position, ['team leader', 'frontliner']) || $jobOrders->isEmpty()) {
            return null;
        }

        $statusCounts = $jobOrders->groupBy('status')->map->count();

        $stats = [
            'total'     => $jobOrders->count(),
            'by_status' => $statusCounts->toArray(),
        ];

        if ($position === 'frontliner') {
            Log::info('Frontliner Created Job Orders', [
                'employee_id'        => auth()->user()->employee_id,
                'user_id'            => auth()->id(),
                'position'           => $position,
                'created_job_orders' => $jobOrders->map(fn ($jo) => [
                    'id'         => $jo->id,
                    'status'     => $jo->status,
                    'created_at' => $jo->created_at,
                ]),
                'job_order_stats'    => $stats,
            ]);
        }

        return $stats;
    }

    private function calculateAverageRating(Employee $employee)
    {
        $allRatings = $employee->performancesAsEmployee
            ->flatMap(fn ($perf) => $perf->ratings)
            ->pluck('performanceRating.scale')
            ->filter();

        return $allRatings->count() ? round($allRatings->avg(), 2) : null;
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|max:255',
            'suffix'      => 'nullable|string|max:10',
        ]);

        $employee->update($validated);

        return redirect()->back()->with('success', 'Profile updated!');
    }
}
