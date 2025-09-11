<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lastName' => 'required|string|max:255',
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:255',
            'dateOfBirth' => 'required|date',
            'email' => 'nullable|email|max:255',
            'contactNumber' => 'nullable|string|max:255',
            'positionId' => 'required|exists:positions,id',
            'region' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zipCode' => 'nullable|string|max:255',
            'detailedAddress' => 'nullable|string',
            
            'emergencyLastName' => 'nullable|string|max:255',
            'emergencyFirstName' => 'nullable|string|max:255',
            'emergencyMiddleName' => 'nullable|string|max:255',
            'emergencySuffix' => 'nullable|string|max:255',
            'emergencyContactNumber' => 'nullable|string|max:255',
            'emergencyRelation' => 'nullable|string|max:255',
            
            'sssNumber' => 'nullable|string|max:255',
            'philhealthNumber' => 'nullable|string|max:255',
            'pagibigNumber' => 'nullable|string|max:255',
            'tin' => 'nullable|string|max:255',
            'dateHired' => 'nullable|date',
            'regularizationDate' => 'nullable|date',
            'endOfContract' => 'nullable|date',
            
            'salary' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Convert camelCase field names to snake_case for database
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        $snakeCaseData = [];
        foreach ($validated as $key => $value) {
            $snakeCaseKey = Str::snake($key);
            $snakeCaseData[$snakeCaseKey] = $value;
        }
        
        return $snakeCaseData;
    }
}