<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'subject'              => ['required', 'string', 'max:255'],
            'location'             => ['required', 'string', 'max:255'],
            'infraction_type'      => ['required', 'string'],
            'occured_at'           => ['required', 'date'],
            'description'          => ['required', 'string'],
            'involved_employees'   => ['nullable', 'array'],
            'involved_employees.*' => ['exists:employees,id'],
        ];
    }
}
