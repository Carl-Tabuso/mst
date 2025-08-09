<?php

namespace App\Enums;

use Illuminate\Http\JsonResponse;

enum UserPermission: string
{
    case CreateJobOrder                   = 'create:job_order';
    case UpdateJobOrder                   = 'update:job_order';
    case SubmitJobOrderCorrection         = 'submit:job_order_correction';
    case ApproveJobOrderCorrection        = 'approve:job_order_correction';
    case FillOutSafetyInspectionChecklist = 'fill:safety_inspection_checklist';
    case AssignHaulingPersonnel           = 'assign:hauling_personnel';
    case AssignAppraisers                 = 'assign:appraisers';
    case SetHaulingDuration               = 'set:hauling_duration';
    case ViewActivityLogs                 = 'view:activiy_logs';
    case ViewAnyJobOrder                  = 'view:any_job_order';

    public function getLabel(): string
    {
        return match ($this) {
            self::CreateJobOrder                   => 'Create Job Order',
            self::UpdateJobOrder                   => 'Update Job Order',
            self::SubmitJobOrderCorrection         => 'Submit Job Order Correction',
            self::ApproveJobOrderCorrection        => 'Approve Job Order Correction',
            self::FillOutSafetyInspectionChecklist => 'Fill-out Safety Inspection Checklist',
            self::AssignHaulingPersonnel           => 'Assign Hauling Personnel',
            self::AssignAppraisers                 => 'Assign Hauling Appraisers',
            self::SetHaulingDuration               => 'Set Hauling Duration',
            self::ViewActivityLogs                 => 'View Activity Logs',
            self::ViewAnyJobOrder                  => 'View Any Job Order',
        };
    }

    public static function getFrontlinerPermissions(): array
    {
        return [
            self::CreateJobOrder,
            self::UpdateJobOrder,
            self::SubmitJobOrderCorrection,
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
            self::ViewAnyJobOrder,
        ];
    }

    public static function getHeadFrontlinerPermissions(): array
    {
        return [
            self::ApproveJobOrderCorrection,
            self::ViewAnyJobOrder,
        ];
    }

    public static function getITAdminPermissions(): array
    {
        return [
            self::ViewActivityLogs,
        ];
    }

    public static function forFrontendMapping(): JsonResponse
    {
        $permissions = array_map(fn ($case) => [
            'id'    => $case->value,
            'label' => $case->getLabel(),
        ], self::cases());

        return response()->json($permissions);
    }
}
