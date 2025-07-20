<?php

namespace App\Http\Requests;

use App\Enums\JobOrderStatus;
use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo(UserPermission::UpdateJobOrder);
    }

    public function rules(): array
    {
        // it's probably just the status we're updating, coming from the client
        // because the changes to each attribute should be map out for corrections?
        return [
            'status' => ['sometimes', 'string', Rule::in(JobOrderStatus::cases())],
        ];
    }
}
