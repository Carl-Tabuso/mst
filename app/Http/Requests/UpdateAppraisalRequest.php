<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppraisalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateAppraisalInformation', $this->form4);
    }

    public function rules(): array
    {
        return [
            'appraisers'     => ['required', 'array'],
            'appraised_date' => ['required', 'date', 'before_or_equal:'.today()->endOfDay()],
        ];
    }
}
