<?php

namespace App\Observers;

use App\Enums\IncidentStatus;
use App\Models\Form3Hauling;
use App\Models\Incident;

class Form3HaulingObserver
{
    public function created(Form3Hauling $hauling)
    {
        $hauling->load('form3.form4.jobOrder');

        Incident::create([
            'form3_hauling_id' => $hauling->id,
            'created_by'       => null,
            'status'           => IncidentStatus::Draft,
            'subject'          => 'Incident Report for Hauling '.$hauling->date->format('Y-m-d'),
            'location'         => 'To be determined',
            'infraction_type'  => 'To be determined',
            'occured_at'       => now(),
            'description'      => 'Auto-generated incident report for hauling operation',
            'is_read'          => false,
        ]);
    }
}
