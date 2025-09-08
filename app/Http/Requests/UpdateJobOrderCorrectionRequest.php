<?php

namespace App\Http\Requests;

use App\Enums\JobOrderCorrectionRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobOrderCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->correction);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(JobOrderCorrectionRequestStatus::cases())],
        ];
    }
}
