<?php
namespace App\Http\Controllers;

use App\Enums\ActivityLogName;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use App\Models\PerformanceRating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ArchiveController extends Controller
{
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'search'    => 'nullable|string|max:255',
            // Change this to accept array of types
            'type'      => ['nullable', 'array'],
            'type.*'    => ['string', Rule::in(['job_order', 'user', 'employee', 'correction'])],
            'sort'      => ['nullable', 'string', Rule::in([
                'archived_at_desc', 'archived_at_asc',
                'name_asc', 'name_desc',
                'type_asc', 'type_desc',
            ])],
            'per_page'  => 'nullable|integer|min:5|max:100',
            'page'      => 'nullable|integer|min:1',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        $perPage    = $validated['per_page'] ?? 10;
        $search     = $validated['search'] ?? null;
        // Handle array of types
        $typeFilter = $validated['type'] ?? null;
        $sort       = $validated['sort'] ?? 'archived_at_desc';
        $dateFrom   = $validated['date_from'] ?? null;
        $dateTo     = $validated['date_to'] ?? null;

        $archivedItems = collect();

        // Helper closure for date filtering
        $applyDateFilter = function ($query) use ($dateFrom, $dateTo) {
            if ($dateFrom && $dateTo) {
                $query->whereBetween('archived_at', [
                    $dateFrom . ' 00:00:00',
                    $dateTo . ' 23:59:59',
                ]);
            }
        };

        // Job Orders - Check if type filter is null or contains 'job_order'
        if (!$typeFilter || in_array('job_order', $typeFilter)) {
            $jobOrders = JobOrder::onlyTrashed()
                ->with(['creator.position', 'serviceable'])
                ->when($search, function ($query) use ($search) {
                    $searchWildcard = "%{$search}%";
                    $query->where(function ($subQuery) use ($searchWildcard) {
                        $subQuery->whereLike('client', $searchWildcard)
                            ->orWhereLike('address', $searchWildcard)
                            ->orWhereLike('contact_no', $searchWildcard)
                            ->orWhereLike('contact_person', $searchWildcard)
                            ->orWhere('id', 'like', $searchWildcard);
                    });
                })
                ->when($dateFrom && $dateTo, $applyDateFilter)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'                   => $item->id,
                        'type'                 => 'job_order',
                        'name'                 => "JO-" . str_pad($item->id, 9, '0', STR_PAD_LEFT),
                        'display_name'         => $item->client,
                        'archived_at'          => $item->archived_at,
                        'archived_by_name'     => $item->creator?->full_name ?? 'Unknown',
                        'archived_by_position' => $item->creator?->position?->name ?? 'Unknown Position',
                        'model_data'           => $item,
                    ];
                });
            $archivedItems = $archivedItems->concat($jobOrders);
        }

        // Users - Check if type filter is null or contains 'user'
        if (!$typeFilter || in_array('user', $typeFilter)) {
            $users = User::onlyTrashed()
                ->with(['employee.position'])
                ->when($search, function ($query) use ($search) {
                    $searchWildcard = "%{$search}%";
                    $query->where(function ($subQuery) use ($searchWildcard) {
                        $subQuery->whereLike('email', $searchWildcard)
                            ->orWhereHas('employee', function ($q) use ($searchWildcard) {
                                $q->whereLike('first_name', $searchWildcard)
                                    ->orWhereLike('last_name', $searchWildcard);
                            });
                    });
                })
                ->when($dateFrom && $dateTo, $applyDateFilter)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'                   => $item->id,
                        'type'                 => 'user',
                        'name'                 => $item->email,
                        'display_name'         => $item->employee?->full_name ?? $item->email,
                        'archived_at'          => $item->deleted_at,
                        'archived_by_name'     => 'System',
                        'archived_by_position' => 'System',
                        'model_data'           => $item,
                    ];
                });
            $archivedItems = $archivedItems->concat($users);
        }

        // Employees - Check if type filter is null or contains 'employee'
        if (!$typeFilter || in_array('employee', $typeFilter)) {
            $employees = Employee::onlyTrashed()
                ->with(['position'])
                ->when($search, function ($query) use ($search) {
                    $searchWildcard = "%{$search}%";
                    $query->where(function ($subQuery) use ($searchWildcard) {
                        $subQuery->whereLike('first_name', $searchWildcard)
                            ->orWhereLike('last_name', $searchWildcard);
                    });
                })
                ->when($dateFrom && $dateTo, $applyDateFilter)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'                   => $item->id,
                        'type'                 => 'employee',
                        'name'                 => $item->employee_code ?? "EMP-{$item->id}",
                        'display_name'         => $item->full_name,
                        'archived_at'          => $item->archived_at,
                        'archived_by_name'     => 'HR Department',
                        'archived_by_position' => 'Human Resources',
                        'model_data'           => $item,
                    ];
                });
            $archivedItems = $archivedItems->concat($employees);
        }

        // Corrections - Check if type filter is null or contains 'correction'
        if (!$typeFilter || in_array('correction', $typeFilter)) {
            $corrections = JobOrderCorrection::onlyTrashed()
                ->with(['jobOrder'])
                ->when($search, function ($query) use ($search) {
                    $searchWildcard = "%{$search}%";
                    $query->where(function ($subQuery) use ($searchWildcard) {
                        $subQuery->where('id', 'like', $searchWildcard)
                            ->orWhereHas('jobOrder', function ($q) use ($searchWildcard) {
                                $q->whereLike('client', $searchWildcard);
                            });
                    });
                })
                ->when($dateFrom && $dateTo, $applyDateFilter)
                ->get()
                ->map(function ($item) {
                    $relatedJobOrder = $item->jobOrder ? "JO-" . str_pad($item->jobOrder->id, 9, '0', STR_PAD_LEFT) : '';
                    return [
                        'id'                   => $item->id,
                        'type'                 => 'correction',
                        'name'                 => "CORR-{$item->id}",
                        'display_name'         => "Correction for {$relatedJobOrder}",
                        'archived_at'          => $item->archived_at,
                        'archived_by_name'     => 'System',
                        'archived_by_position' => 'System',
                        'model_data'           => $item,
                    ];
                });
            $archivedItems = $archivedItems->concat($corrections);
        }

        // Apply sorting
        $sortedItems = $this->applySorting($archivedItems, $sort);

        // Manual pagination
        $currentPage = (int) $request->get('page', 1);
        $total       = $sortedItems->count();
        $items       = $sortedItems->slice(($currentPage - 1) * $perPage, $perPage);

        $paginatedData = [
            'data'         => $items->values(),
            'current_page' => $currentPage,
            'last_page'    => (int) ceil($total / $perPage),
            'per_page'     => $perPage,
            'total'        => $total,
            'from'         => ($currentPage - 1) * $perPage + 1,
            'to'           => min($currentPage * $perPage, $total),
        ];

        return Inertia::render('archive/page/Index', [
            'archivedItems' => $paginatedData,
            'search'        => $search,
            'typeFilter'    => $typeFilter,
            'sort'          => $sort,
            'filters'       => [
                'search'    => $search,
                'type'      => $typeFilter,
                'sort'      => $sort,
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
            ],
        ]);
    }

    private function applySorting($items, string $sort)
    {
        switch ($sort) {
            case 'archived_at_asc':
                return $items->sortBy('archived_at');
            case 'name_asc':
                return $items->sortBy('name');
            case 'name_desc':
                return $items->sortByDesc('name');
            case 'type_asc':
                return $items->sortBy('type');
            case 'type_desc':
                return $items->sortByDesc('type');
            case 'archived_at_desc':
            default:
                return $items->sortByDesc('archived_at');
        }
    }

    public function restore(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer',
            'type'  => 'required|string|in:job_order,user,employee,correction',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $modelClass = $this->getModelClass($validated['type']);

            if ($modelClass) {
                $modelClass::withTrashed()
                    ->whereIn('id', $validated['ids'])
                    ->restore();

                // Log the restore action
                $user = $request->user();
                if ($user) {
                    activity()
                        ->useLog(ActivityLogName::ArchiveAccess->value ?? 'archive_access')
                        ->causedBy($user)
                        ->event('restored')
                        ->withProperties([
                            'type'  => $validated['type'],
                            'ids'   => $validated['ids'],
                            'count' => count($validated['ids']),
                        ])
                        ->log(__('activity.archive.restored', [
                            'causer' => $user->employee->full_name ?? $user->email,
                            'type'   => $validated['type'],
                            'count'  => count($validated['ids']),
                        ]));
                }
            }
        });

        return redirect()->back()->with('success', 'Items restored successfully.');
    }

    public function forceDelete(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer',
            'type'  => 'required|string|in:job_order,user,employee,correction',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $modelClass = $this->getModelClass($validated['type']);

            if ($modelClass) {
                $modelClass::withTrashed()
                    ->whereIn('id', $validated['ids'])
                    ->forceDelete();

                // Log the permanent delete action
                $user = $request->user();
                if ($user) {
                    activity()
                        ->useLog(ActivityLogName::ArchiveAccess->value ?? 'archive_access')
                        ->causedBy($user)
                        ->event('force_deleted')
                        ->withProperties([
                            'type'  => $validated['type'],
                            'ids'   => $validated['ids'],
                            'count' => count($validated['ids']),
                        ])
                        ->log(__('activity.archive.force_deleted', [
                            'causer' => $user->employee->full_name ?? $user->email,
                            'type'   => $validated['type'],
                            'count'  => count($validated['ids']),
                        ]));
                }
            }
        });

        return redirect()->back()->with('success', 'Items permanently deleted.');
    }

    private function getModelClass(string $type): ?string
    {
        return match ($type) {
            'job_order' => JobOrder::class,
            'user' => User::class,
            'employee' => Employee::class,
            'correction' => JobOrderCorrection::class,
            default => null,
        };
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'search'    => 'nullable|string|max:255',
            'type'      => ['nullable', 'array'],
            'type.*'    => ['string', Rule::in(['job_order', 'user', 'employee', 'correction'])],
            'sort'      => ['nullable', 'string', Rule::in([
                'archived_at_desc', 'archived_at_asc',
                'name_asc', 'name_desc',
                'type_asc', 'type_desc',
            ])],
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        $search     = $validated['search'] ?? null;
        $typeFilter = $validated['type'] ?? null;
        $sort       = $validated['sort'] ?? 'archived_at_desc';
        $dateFrom   = $validated['date_from'] ?? null;
        $dateTo     = $validated['date_to'] ?? null;

        $archivedItems = collect();

        // Helper closure for date filtering
        $applyDateFilter = function ($query) use ($dateFrom, $dateTo) {
            if ($dateFrom && $dateTo) {
                $query->whereBetween('archived_at', [
                    $dateFrom . ' 00:00:00',
                    $dateTo . ' 23:59:59',
                ]);
            }
        };

        // Job Orders
        if (!$typeFilter || in_array('job_order', $typeFilter)) {
            $jobOrders = JobOrder::onlyTrashed()
                ->with(['creator.position', 'serviceable'])
                ->when($search, function ($query) use ($search) {
                    $searchWildcard = "%{$search}%";
                    $query->where(function ($subQuery) use ($searchWildcard) {
                        $subQuery->whereLike('client', $searchWildcard)
                            ->orWhereLike('address', $searchWildcard)
                            ->orWhereLike('contact_no', $searchWildcard)
                            ->orWhereLike('contact_person', $searchWildcard)
                            ->orWhere('id', 'like', $searchWildcard);
                    });
                })
                ->when($dateFrom && $dateTo, $applyDateFilter)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'                   => $item->id,
                        'type'                 => 'job_order',
                        'name'                 => "JO-" . str_pad($item->id, 9, '0', STR_PAD_LEFT),
                        'display_name'         => $item->client,
                        'archived_at'          => $item->archived_at,
                        'archived_by_name'     => $item->creator?->full_name ?? 'Unknown',
                        'archived_by_position' => $item->creator?->position?->name ?? 'Unknown Position',
                    ];
                });
            $archivedItems = $archivedItems->concat($jobOrders);
        }

        // Users
        if (!$typeFilter || in_array('user', $typeFilter)) {
            $users = User::onlyTrashed()
                ->with(['employee.position'])
                ->when($search, function ($query) use ($search) {
                    $searchWildcard = "%{$search}%";
                    $query->where(function ($subQuery) use ($searchWildcard) {
                        $subQuery->whereLike('email', $searchWildcard)
                            ->orWhereHas('employee', function ($q) use ($searchWildcard) {
                                $q->whereLike('first_name', $searchWildcard)
                                    ->orWhereLike('last_name', $searchWildcard);
                            });
                    });
                })
                ->when($dateFrom && $dateTo, $applyDateFilter)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'                   => $item->id,
                        'type'                 => 'user',
                        'name'                 => $item->email,
                        'display_name'         => $item->employee?->full_name ?? $item->email,
                        'archived_at'          => $item->deleted_at,
                        'archived_by_name'     => 'System',
                        'archived_by_position' => 'System',
                    ];
                });
            $archivedItems = $archivedItems->concat($users);
        }

        // Employees
        if (!$typeFilter || in_array('employee', $typeFilter)) {
            $employees = Employee::onlyTrashed()
                ->with(['position'])
                ->when($search, function ($query) use ($search) {
                    $searchWildcard = "%{$search}%";
                    $query->where(function ($subQuery) use ($searchWildcard) {
                        $subQuery->whereLike('first_name', $searchWildcard)
                            ->orWhereLike('last_name', $searchWildcard);
                    });
                })
                ->when($dateFrom && $dateTo, $applyDateFilter)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'                   => $item->id,
                        'type'                 => 'employee',
                        'name'                 => $item->employee_code ?? "EMP-{$item->id}",
                        'display_name'         => $item->full_name,
                        'archived_at'          => $item->archived_at,
                        'archived_by_name'     => 'HR Department',
                        'archived_by_position' => 'Human Resources',
                    ];
                });
            $archivedItems = $archivedItems->concat($employees);
        }

        // Corrections
        if (!$typeFilter || in_array('correction', $typeFilter)) {
            $corrections = JobOrderCorrection::onlyTrashed()
                ->with(['jobOrder'])
                ->when($search, function ($query) use ($search) {
                    $searchWildcard = "%{$search}%";
                    $query->where(function ($subQuery) use ($searchWildcard) {
                        $subQuery->where('id', 'like', $searchWildcard)
                            ->orWhereHas('jobOrder', function ($q) use ($searchWildcard) {
                                $q->whereLike('client', $searchWildcard);
                            });
                    });
                })
                ->when($dateFrom && $dateTo, $applyDateFilter)
                ->get()
                ->map(function ($item) {
                    $relatedJobOrder = $item->jobOrder ? "JO-" . str_pad($item->jobOrder->id, 9, '0', STR_PAD_LEFT) : '';
                    return [
                        'id'                   => $item->id,
                        'type'                 => 'correction',
                        'name'                 => "CORR-{$item->id}",
                        'display_name'         => "Correction for {$relatedJobOrder}",
                        'archived_at'          => $item->archived_at,
                        'archived_by_name'     => 'System',
                        'archived_by_position' => 'System',
                    ];
                });
            $archivedItems = $archivedItems->concat($corrections);
        }

        // Apply sorting
        $sortedItems = $this->applySorting($archivedItems, $sort);

        // Generate filename with current date and filters
        $filename = 'archived-items-' . now()->format('Y-m-d-H-i-s');
        
        if ($typeFilter && count($typeFilter) > 0) {
            $filename .= '-' . implode('-', $typeFilter);
        }
        
        $filename .= '.xlsx';

        return Excel::download(
            new \App\Exports\ArchivedItemsExport($sortedItems),
            $filename
        );
    }
}