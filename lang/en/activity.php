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
];
