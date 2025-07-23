<?php

namespace App\Http\Requests;

use App\Enums\JobOrderStatus;
use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCancelledJobOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo(UserPermission::UpdateJobOrder);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(JobOrderStatus::getCancelledStatuses())],
            'reason' => ['required', 'string'],
        ];
    }
}
