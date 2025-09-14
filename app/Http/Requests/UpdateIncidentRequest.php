<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'infraction_type' => ['required', 'string'],
            'occured_at' => ['required', 'date'],
            'description' => ['required', 'string'],
            'involved_employees' => ['nullable', 'array'],
            'involved_employees.*' => ['exists:employees,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages()
    {
        return [
            'subject.required' => 'The incident subject is required.',
            'location.required' => 'The incident location is required.',
            'infraction_type.required' => 'Please select an infraction type.',
            'occured_at.required' => 'Please specify when the incident occurred.',
            'occured_at.date' => 'The occurrence date must be a valid date.',
            'description.required' => 'Please provide a description of the incident.',
            'involved_employees.*.exists' => 'One or more selected employees do not exist.',
        ];
    }
}