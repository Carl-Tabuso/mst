<?php

namespace App\Enums;

use Illuminate\Http\JsonResponse;

enum UserPermission: string
{
    case CreateJobOrder                   = 'create:job_order';
    case UpdateJobOrder                   = 'update:job_order';
    case CreateJobOrderCorrection         = 'create:job_order_correction';
    case UpdateJobOrderCorrection         = 'update:job_order_correction';
    case FillOutSafetyInspectionChecklist = 'fill:safety_inspection_checklist';
    case AssignHaulingPersonnel           = 'assign:hauling_personnel';
    case AssignAppraisers                 = 'assign:appraisers';
    case SetHaulingDuration               = 'set:hauling_duration';
    case ViewActivityLogs                 = 'view:activity_logs';
    case ViewAnyJobOrder                  = 'view:any_job_order';
    case ManageUsers                      = 'manage:users';
    case ManageIncidentReports            = 'manage:incident_reports';
    case ManageEmployees                  = 'manage:employees';
    case ViewReportsAnalytics             = 'view:reports_analytics';
    case ViewPerformances                 = 'view:performances';
    case ViewAnyJobOrderCorrection        = 'view:any_job_order_correction';

    case ViewAnyEmployeeRating            = 'view:any_employee_rating';
    case CreateEmployeeRating             = 'create:employee_rating';
    case UpdateEmployeeRating             = 'update:employee_rating';
    case DeleteEmployeeRating             = 'delete:employee_rating';
    case ExportEmployeeRating             = 'export:employee_rating';
    case RestoreArchivedJobOrder          = 'restore:archived_job_order';
    case ForceDeleteJobOrder              = 'force_delete:job_order';

    public function getLabel(): string
    {
        return match ($this) {
            self::CreateJobOrder                   => 'Create Job Order',
            self::UpdateJobOrder                   => 'Update Job Order',
            self::CreateJobOrderCorrection         => 'Create Job Order Correction',
            self::UpdateJobOrderCorrection         => 'Update Job Order Correction',
            self::FillOutSafetyInspectionChecklist => 'Fill-out Safety Inspection Checklist',
            self::AssignHaulingPersonnel           => 'Assign Hauling Personnel',
            self::AssignAppraisers                 => 'Assign Hauling Appraisers',
            self::SetHaulingDuration               => 'Set Hauling Duration',
            self::ViewActivityLogs                 => 'View Activity Logs',
            self::ViewAnyJobOrder                  => 'View Any Job Order',
            self::ManageUsers                      => 'Manage Employee Account',
            self::ManageIncidentReports            => 'Manage Incident Reports',
            self::ManageEmployees                  => 'Manage Employees',
            self::ViewReportsAnalytics             => 'View Reports And Analytics',
            self::ViewPerformances                 => 'View Performances',
            self::ViewAnyJobOrderCorrection        => 'View Any Job Order Corrections',

            self::ViewAnyEmployeeRating            => 'View Any Employee Rating',
            self::CreateEmployeeRating             => 'Create Employee Rating',
            self::UpdateEmployeeRating             => 'Update Employee Rating',
            self::DeleteEmployeeRating             => 'Delete Employee Rating',
            self::ExportEmployeeRating             => 'Export Employee Rating',
            self::RestoreArchivedJobOrder          => 'Restore Archived Job Order',
            self::ForceDeleteJobOrder              => 'Force Delete Job Order',
        };
    }

    public static function getFrontlinerPermissions(): array
    {
        return [
            self::CreateJobOrder,
            self::UpdateJobOrder,
            self::CreateJobOrderCorrection,
        ];
    }

    public static function getDispatcherPermissions(): array
    {
        return [
            self::AssignAppraisers,
            self::AssignHaulingPersonnel,
            self::SetHaulingDuration,
            self::ViewAnyJobOrder,
        ];
    }

    public static function getTeamLeaderPermissions(): array
    {
        return [
            self::FillOutSafetyInspectionChecklist,
            self::ManageIncidentReports,
            self::ViewAnyJobOrder,
            self::ViewAnyEmployeeRating,
            self::CreateEmployeeRating,
            self::UpdateEmployeeRating,
        ];
    }

    public static function getHeadFrontlinerPermissions(): array
    {
        return [
            self::UpdateJobOrderCorrection,
            self::ViewAnyJobOrder,
            self::ViewActivityLogs,
            self::ViewReportsAnalytics,
            self::ViewPerformances,
            self::ViewAnyJobOrderCorrection,
            self::UpdateJobOrderCorrection,
            self::ManageIncidentReports,
            self::RestoreArchivedJobOrder,
            self::ForceDeleteJobOrder,
        ];
    }

    public static function getITAdminPermissions(): array
    {
        return [
            self::ViewActivityLogs,
            self::ManageUsers,
        ];
    }

    public static function getConsultantPermissions(): array
    {
        return [
            self::ManageIncidentReports,
            self::ViewReportsAnalytics,
            self::ViewPerformances,
            self::ViewAnyEmployeeRating,
            self::ExportEmployeeRating,
        ];
    }

    public static function getHumanResourcePermissions(): array
    {
        return [
            self::ManageEmployees,
            self::ManageIncidentReports,
        ];
    }

    public static function getSafetyOfficerPermissions(): array
    {
        return [
            self::ManageIncidentReports,
        ];
    }

    public static function getRegularPermissions(): array
    {
        return [
            self::ViewAnyEmployeeRating,
            self::CreateEmployeeRating,
        ];
    }

    public static function forFrontendMapping(): JsonResponse
    {
        $permissions = array_map(fn($case) => [
            'id'    => $case->value,
            'label' => $case->getLabel(),
        ], self::cases());

        return response()->json($permissions);
    }
}
