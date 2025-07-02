<?php

namespace App\Http\Requests;

use App\Enums\JobOrderStatus;
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
            'status'         => ['sometimes', 'nullable', Rule::in(JobOrderStatus::cases())],
            'appraisers'     => ['sometimes', 'array'],
            'appraised_date' => ['sometimes', 'nullable', 'date'],
            'payment_date'   => ['sometimes', 'nullable', 'date'],
            'bid_bond'       => ['sometimes', 'nullable', 'integer'],
            'or_number'      => ['sometimes', 'nullable', 'string'],
            'approved_date'  => ['sometimes', 'nullable', 'date'],
            'truck_no'       => ['sometimes', 'nullable', 'string'],
            'payment_type'   => ['sometimes', 'nullable', 'string'],
            'from'           => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d H:i:s'],
            'to'             => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d H:i:s'],
            'team_leader'    => ['sometimes', 'nullable', Rule::exists((new Employee)->getTable(), 'id')],
            'team_driver'    => ['sometimes', 'nullable', Rule::exists((new Employee)->getTable(), 'id')],
            'safety_officer' => ['sometimes', 'nullable', Rule::exists((new Employee)->getTable(), 'id')],
            'team_mechanic'  => ['sometimes', 'nullable', Rule::exists((new Employee)->getTable(), 'id')],
            'haulers'        => ['sometimes', 'array', 'max:12'],
        ];
    }
}
