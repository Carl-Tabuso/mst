<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobOrderCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->correction);
    }

    public function rules(): array
    {
        return [
            'status'     => ['required', 'in:approved,rejected'],
            'new_values' => ['required'],
        ];
    }
}
