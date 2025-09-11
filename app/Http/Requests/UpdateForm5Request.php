<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateForm5Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assigned_person'   => 'nullable|exists:employees,id',
            'items'             => 'nullable|array',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity'  => 'required|integer|min:1',
            'status'            => 'sometimes|in:in_progress,completed',
        ];
    }
}
