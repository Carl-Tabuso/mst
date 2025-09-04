<?php
namespace App\Enums;

enum ActivityLogName: string {
    case Auth               = 'auth';
    case Form4              = 'form4';
    case Form5              = 'form5';
    case ITService          = 'it_service';
    case TicketBatchArchive = 'ticket_batch_archive';

    case ArchiveAccess            = 'archive_access';
    case TicketRestore            = 'ticket_restore';
    case TicketBatchRestore       = 'ticket_batch_restore';
    case TicketForceDelete        = 'ticket_force_delete';
    case TicketBatchForceDelete   = 'ticket_batch_force_delete';
    case TicketBulkRestoreAll     = 'ticket_bulk_restore_all';
    case TicketBulkForceDeleteAll = 'ticket_bulk_force_delete_all';

    public function getLabel(): string
    {
        return match ($this) {
            self::Auth => 'Authentication',
            self::Form4 => 'Waste Management',
            self::Form5 => 'Other Services',
            self::ITService => 'IT Services',
            self::TicketBatchArchive => 'Ticket Batch Archival',

            // Archive-related labels
            self::ArchiveAccess => 'Archive Access',
            self::TicketRestore => 'Ticket Restoration',
            self::TicketBatchRestore => 'Ticket Batch Restoration',
            self::TicketForceDelete => 'Ticket Permanent Deletion',
            self::TicketBatchForceDelete => 'Ticket Batch Permanent Deletion',
            self::TicketBulkRestoreAll => 'Bulk Restore All Tickets',
            self::TicketBulkForceDeleteAll => 'Bulk Permanent Delete All Tickets',
        };
    }
}
