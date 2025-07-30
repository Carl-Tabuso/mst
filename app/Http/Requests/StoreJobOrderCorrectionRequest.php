<?php

namespace App\Http\Requests;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobOrderCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo(UserPermission::SubmitJobOrderCorrection);
    }

    public function rules(): array
    {
        return [
            'date_time'        => ['required', 'date'],
            'client'           => ['required', 'string'],
            'address'          => ['required', 'string'],
            'department'       => ['required', 'string'],
            'contact_position' => ['required', 'string'],
            'contact_person'   => ['required', 'string'],
            'contact_no'       => ['required', 'digits:11'],
            'payment_date'     => ['sometimes', 'date'],
            'or_number'        => ['sometimes', 'string'],
            'bid_bond'         => ['sometimes'],
            'payment_type'     => ['sometimes', 'string'],
            'approved_date'    => ['sometimes', 'date'],
            'reason'           => ['sometimes', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'bid_bond' => (float) str_replace(',', '', $this->bid_bond),
        ]);
    }
}
