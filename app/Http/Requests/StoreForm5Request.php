<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreForm5Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_time'         => 'required|date',
            'client'            => 'required|string|max:255',
            'address'           => 'required|string|max:255',
            'department'        => 'required|string|max:255',
            'contact_no'        => 'required|string|max:20',
            'contact_person'    => 'required|string|max:255',
            'contact_position'  => 'required|string|max:255',
            'description'       => 'nullable|string|max:255',
            'assigned_person'   => 'nullable|exists:employees,id',
            'purpose'           => 'required|string',
            'items'             => 'nullable|array',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity'  => 'required|integer|min:1',
        ];
    }
}
