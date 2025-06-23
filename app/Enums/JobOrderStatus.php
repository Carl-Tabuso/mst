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
    case ForPersonnelAssignment = 'for personnel assignment';
    case ForSafetyInspection    = 'for safety inspection';
    case ForVerification        = 'for verification';
    case Verified               = 'verified';
    case ForAppraisal           = 'for appraisal';

    public static function getCancelledStatuses(): array
    {
        return [
            self::Failed,
            self::Dropped,
            self::Closed,
        ];
    }
}
