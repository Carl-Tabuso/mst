<?php

namespace App\Http\Requests;

use App\Enums\UserPermission;
use App\Models\JobOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobOrderCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo(UserPermission::SubmitJobOrderCorrection);
    }

    public function rules(): array
    {
        return [
            'job_order_id' => ['required', Rule::exists((new JobOrder)->getTable(), 'id')],
            'reason'       => ['required', 'string'],
        ];
    }
}
