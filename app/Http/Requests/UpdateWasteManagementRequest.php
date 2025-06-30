<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWasteManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_time'      => ['sometimes', 'required', 'date', 'date_format:Y-m-d H:i:s'],
            'client'         => ['sometimes', 'required', 'string'],
            'address'        => ['sometimes', 'required', 'string'],
            'department'     => ['sometimes', 'required', 'string'],
            'position'       => ['sometimes', 'required', 'string'],
            'contact_person' => ['sometimes', 'required', 'string'],
            'contact_no'     => ['sometimes', 'required', 'digits:11'],
            'payment_date'   => ['sometimes', 'required', 'date', 'date_format:Y-m-d H:i:s'],
            'bid_bond'       => ['sometimes', 'required', 'string'],
            'or_number'      => ['sometimes', 'required', 'string'],
            'approved_date'  => ['sometimes', 'required', 'date', 'date_format:Y-m-d H:i:s'],
            'truck_no'       => ['sometimes', 'required', 'string'],
            'payment_type'   => ['sometimes', 'required', 'string'],
            'from'           => ['sometimes', 'required', 'date', 'date_format:Y-m-d H:i:s'],
            'to'             => ['sometimes', 'required', 'date', 'date_format:Y-m-d H:i:s'],
            'team_leader'    => ['sometimes', 'required', Rule::exists((new Employee)->table(), 'id')],
            'team_driver'    => ['sometimes', 'required', Rule::exists((new Employee)->table(), 'id')],
            'safety_officer' => ['sometimes', 'required', Rule::exists((new Employee)->table(), 'id')],
            'team_mechanic'  => ['sometimes', 'required', Rule::exists((new Employee)->table(), 'id')],
        ];
    }
}
