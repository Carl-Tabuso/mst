<?php

namespace App\Http\Requests;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSafetyInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo(UserPermission::FillOutSafetyInspectionChecklist);
    }

    public function rules(): array
    {
        return [
            'is_vehicle_inspection_filled' => ['required', 'boolean'],
            'is_uniform_ppe_filled'        => ['required', 'boolean'],
            'is_tools_equipment_filled'    => ['required', 'boolean'],
            'is_certify'                   => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_certify' => __('You need to checked the certification to move forward.'),
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'isFullyAccomplished' => array_all($this->safe()->all(), fn ($isFilled) => $isFilled),
        ]);
    }
}
