<?php

namespace App\Enums;

enum JobOrderStatus: string
{
    case ForViewing             = 'for viewing';
    case ForProposal            = 'for proposal';
    case ForApproval            = 'for approval';
    case Successful             = 'successful';
    case Failed                 = 'failed';
    case Dropped                = 'dropped';
    case HaulingInProgress      = 'hauling in-progress';
    case OnHold                 = 'on-hold';
    case Closed                 = 'closed';
    case Completed              = 'completed';
    case ForVerification        = 'for verification';
    case Verified               = 'verified';
    case ForAppraisal           = 'for appraisal';
    case PreHauling             = 'pre-hauling';

    public static function getCancelledStatuses(): array
    {
        return [
            self::Failed,
            self::Dropped,
            self::Closed,
        ];
    }

    public static function getPreHaulingStages(): array
    {
        return [
            self::ForViewing,
            self::Dropped,
            self::ForProposal,
            self::Failed,
            self::Successful,
        ];
    }

    public static function getManualStagesUpdate(): array
    {
        return [
            self::Dropped,
            self::ForProposal,
            self::Successful,
            self::Failed,
            self::Completed,
            self::Closed
        ];
    }
}
