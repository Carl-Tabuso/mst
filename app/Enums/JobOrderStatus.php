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
    case OnHold                 = 'on-hold';
    case Closed                 = 'closed';
    case Completed              = 'completed';
    case ForVerification        = 'for verification';
    case Verified = 'verified';

    case ForAppraisal           = 'for appraisal';
    case PreHauling             = 'pre-hauling';
    case InProgress             = 'in-progress';

    public function getLabel(): string
    {
        return match ($this) {
            self::ForViewing        => 'For Viewing',
            self::ForProposal       => 'For Proposal',
            self::ForApproval       => 'For Approval',
            self::Successful        => 'Successful',
            self::Failed            => 'Failed',
            self::Dropped           => 'Dropped',
            self::OnHold            => 'On-Hold',
            self::Closed            => 'Closed',
            self::Completed         => 'Completed',
            self::ForVerification   => 'For Verification',
            self::ForAppraisal      => 'For Appraisal',
            self::PreHauling        => 'Pre-Hauling',
            self::ForCheckUP        => 'For Check Up',
            self::Verified          => 'Verified',
            self::InProgress        => 'In-progress',
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

    public static function getManualStagesUpdate(): array
    {
        return [
            self::Dropped,
            self::ForProposal,
            self::Successful,
            self::Failed,
            self::ForVerification,
            self::Closed,
        ];
    }

    public static function getCanRequestCorrectionStages(): array
    {
        return [
            self::ForVerification,
            self::PreHauling,
            self::InProgress,
            self::OnHold,
            self::Closed,
            self::Completed,
        ];
    }
}
