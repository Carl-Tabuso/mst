<?php

return [

    'archive' => [
        'accessed' => ':causer accessed the archive page showing :total archived job orders',
    ],

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
    ],
];
