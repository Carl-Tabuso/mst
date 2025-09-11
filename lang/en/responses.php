<?php

return [
    'job_order'     => [
        'create' => 'Created Job Order|Ticket: :ticket',
    ],
    'status_update' => 'Successfully set status to :status.',
    'checklist'     => 'Checklist changes has been saved.|:date',
    'change'        => 'Changes has been saved.',
    'archive'       => [
        'ticket'     => 'Succesfully archived ticket :ticket.',
        'correction' => 'Succesfully archived correction request for ticket :ticket.',
    ],
    'batch_archive' => [
        'ticket'     => 'Successfullly archived ticket(s).',
        'correction' => 'Successfullly archived :count correction request(s).',
    ],
    'correction'        => 'Successfully requested for correction.',
    'correction_update' => 'Marked correction request as :status.',
    'truck'             => [
        'create' => 'Saved new truck information.',
        'update' => 'Saved changes :model: :plate_no.',
    ],
    'restore' => [
        'ticket'   => 'Successfully restored ticket :ticket.',
        'employee' => ':employee was successfully restored.',
        'user'     => ':first_name\'s account was successfully restored',
    ],
    'batch_restore' => [
        'ticket'   => 'Successfully restored :count ticket(s).',
        'employee' => 'Successfully restored :count employee(s).',
        'user'     => 'Successfully restored :count user(s).',
    ],
    'permanent_delete' => [
        'ticket'   => 'Ticket :ticket was permanently deleted.',
        'employee' => ':employee was permanently deleted.',
        'user'     => ':first_name\'s account was permanently deleted.',
    ],
];
