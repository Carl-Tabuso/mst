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
];
