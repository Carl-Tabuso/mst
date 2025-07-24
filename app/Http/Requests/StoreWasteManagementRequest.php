<?php

namespace App\Http\Requests;

use App\Enums\JobOrderServiceType;
use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWasteManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo(UserPermission::CreateJobOrder);
    }

    public function rules(): array
    {
        return [
            'service_type'     => ['required', 'string', Rule::in(JobOrderServiceType::cases())],
            'date_time'        => ['required', 'date', 'date_format:Y-m-d H:i:s'],
            'client'           => ['required', 'string'],
            'address'          => ['required', 'string'],
            'department'       => ['required', 'string'],
            'contact_position' => ['required', 'string'],
            'contact_person'   => ['required', 'string'],
            'contact_no'       => ['required', 'digits:11'],
        ];
    }
}
