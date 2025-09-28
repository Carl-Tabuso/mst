<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppraisalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('storeAppraisalInformation', $this->form4);
    }

    public function rules(): array
    {
        return [
            'appraisers'     => ['required', 'array'],
            'appraised_date' => ['required', 'date', 'before_or_equal:'.today()->endOfDay()],
        ];
    }
}
