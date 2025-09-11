<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'date_of_birth' => 'nullable|date',
            'contact_number' => 'nullable|string|max:20',
            'position_id' => 'required|exists:positions,id',

            'region' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'detailed_address' => 'nullable|string|max:500',

            'emergency_last_name' => 'nullable|string|max:255',
            'emergency_first_name' => 'nullable|string|max:255',
            'emergency_middle_name' => 'nullable|string|max:255',
            'emergency_suffix' => 'nullable|string|max:10',
            'emergency_contact_number' => 'nullable|string|max:20',
            'emergency_relation' => 'nullable|string|max:255',

            'sss_number' => 'nullable|string|max:20',
            'philhealth_number' => 'nullable|string|max:20',
            'pagibig_number' => 'nullable|string|max:20',
            'tin' => 'nullable|string|max:20',
            'date_hired' => 'nullable|date',
            'regularization_date' => 'nullable|date',
            'end_of_contract' => 'nullable|date',

            'salary' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',

            'email' => 'nullable|email|unique:employees,email',
            'create_account' => 'nullable|boolean',
            'account_email' => 'nullable|required_if:create_account,true|email|unique:users,email',
        ];
    }
}
