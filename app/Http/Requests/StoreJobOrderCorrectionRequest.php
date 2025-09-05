<?php

namespace App\Http\Requests;

use App\Models\JobOrderCorrection;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobOrderCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', JobOrderCorrection::class);
    }

    public function rules(): array
    {
        $rules = [
            'date_time'        => ['required', 'date'],
            'client'           => ['required', 'string'],
            'address'          => ['required', 'string'],
            'department'       => ['required', 'string'],
            'contact_position' => ['required', 'string'],
            'contact_person'   => ['required', 'string'],
            'contact_no'       => ['required', 'digits:11'],
            'reason'           => ['required', 'string'],
            
            'payment_date'     => ['sometimes', 'required', 'date'],
            'or_number'        => ['sometimes', 'required', 'string'],
            'bid_bond'         => ['sometimes', 'required'],
            'payment_type'     => ['sometimes', 'required', 'string'],
            'approved_date'    => ['sometimes', 'required', 'date'],
            
            'assigned_person'  => ['sometimes', 'required', 'integer', 'exists:employees,id'],
            'purpose'          => ['sometimes', 'required', 'string'],
            'items'            => ['sometimes', 'required', 'array', 'min:1'],
            'items.*.item_name' => ['sometimes', 'required', 'string'],
            'items.*.quantity' => ['sometimes', 'required', 'integer', 'min:1'],
        ];

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'bid_bond' => (float) str_replace(',', '', $this->bid_bond),
        ]);
    }
}