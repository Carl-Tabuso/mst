<?php

namespace App\Enums;

enum ActivityLogName: string
{
    case Auth               = 'auth';
    case Form4              = 'form4';
    case Form5              = 'form5';
    case ITService          = 'it_service';
    case TicketBatchArchive = 'ticket_batch_archive';

    public function getLabel(): string
    {
        return match ($this) {
            self::Auth               => 'Authentication',
            self::Form4              => 'Waste Management',
            self::Form5              => 'Other Services',
            self::ITService          => 'IT Services',
            self::TicketBatchArchive => 'Ticket Batch Archival',
        };
    }
}
