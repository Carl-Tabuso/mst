<?php

namespace App\Enums;

enum JobOrderStatus: string
{
    case ForViewing         = 'for viewing';
    case ForProposal        = 'for proposal';
    case ForApproval        = 'for approval';
    case Successful         = 'successful';
    case Failed             = 'failed';
    case Dropped            = 'dropped';
    case HaulingInProgress  = 'hauling in-progress';
    case OnHold             = 'on-hold';
    case Closed             = 'closed';
    case Completed          = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::ForViewing         => 'For Viewing',
            self::ForProposal        => 'For Proposal',
            self::ForApproval        => 'For Approval',
            self::Successful         => 'Successful',
            self::Failed             => 'Failed',
            self::Dropped            => 'Dropped',
            self::HaulingInProgress  => 'Hauling In-progress',
            self::OnHold             => 'On-hold',
            self::Closed             => 'Closed',
            self::Completed          => 'Completed',
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

    public static function getOptions(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->getLabel();

            return $carry;
        });
    }
}
