<?php

namespace App\Enums;

use Illuminate\Http\JsonResponse;

enum UserPermission: string
{
    case CreateJobOrder                   = 'create jo';
    case UpdateJobOrder                   = 'update jo';
    case SubmitJobOrderCorrection         = 'submit jo correction';
    case ApproveJobOrderCorrection        = 'approve jo correction';
    case FillOutSafetyInspectionChecklist = 'fill out checklist';
    case AssignHaulingPersonnel           = 'assign hauling personnel';
    case AssignAppraisers                 = 'assign appraisers';

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
        ];
    }

    public static function getTeamLeaderPermissions(): array
    {
        return [
            self::FillOutSafetyInspectionChecklist,
        ];
    }

    public static function getHeadFrontlinerPermissions(): array
    {
        return [
            self::ApproveJobOrderCorrection,
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
