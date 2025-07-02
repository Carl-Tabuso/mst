<?php

namespace App\Enums;

enum JobOrderStatus: string
{
    case ForViewing             = 'for viewing';
    case ForCheckUP = 'for check up'; // For IT services
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
    case InProgress  = 'in-progress';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::ForViewing  => 'For Viewing', 
            self::ForCheckUP => 'For Check Up',
            self::ForProposal => 'For Proposal',
            self::ForApproval => 'For Approval',
            self::Successful  => 'Successful',
            self::Failed      => 'Failed',
            self::Dropped     => 'Dropped',
            self::InProgress  => 'In-progress',
            self::OnHold      => 'On-hold',
            self::Closed      => 'Closed',
            self::Completed   => 'Completed',
        };
    }

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
}
