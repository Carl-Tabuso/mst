<?php

return [
    'job_order' => [
        'created'  => ':causer created a new job order of ticket :ticket.',
        'updated'  => ':causer updated the job order information of ticket :ticket.',
        'archived' => [
            'single' => ':causer archived the job order of ticket :ticket.',
            'batch'  => ':causer archived :ticket_count job order tickets.',
        ],
        'deleted' => [
            'single' => ':causer permanently deleted the job order of ticket :ticket.',
            'batch'  => ':causer permanently deleted :ticket_count job order tickets.',
        ],
        'restored' => [
            'single' => ':causer restored the job order of ticket :ticket.',
            'batch'  => ':causer restored :ticket_count job order tickets.',
        ],
    ],
    'truck' => [
        'created'  => ':causer added truck :plate_no.',
        'updated'  => ':causer updated the information of truck :plate_no.',
        'archived' => ':causer archived truck :plate_no.',
        'deleted'  => ':causer permanently removed truck :plate_no.',
        'restored' => ':causer restored truck :plate_no.',
    ],
    'correction' => [
        'created' => ':causer request a ticket correction.',
        'updated' => ':causer updated the status of a ticket correction.',
    ],
];
