<?php

namespace App\Http\Requests;

use App\Enums\JobOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWasteManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isForAppraisal = $this->isOfStatus(JobOrderStatus::ForAppraisal);
        $isSuccessful   = $this->isOfStatus(JobOrderStatus::Successful);
        $isPreHauling   = $this->isOfStatus(JobOrderStatus::PreHauling);

        return [
            'status'         => ['required', 'string', Rule::in(JobOrderStatus::cases())],
            'appraisers'     => [Rule::requiredIf($isForAppraisal), 'array'],
            'appraised_date' => [Rule::requiredIf($isForAppraisal), 'date', 'before_or_equal:'.today()->startOfDay()],
            'payment_date'   => [Rule::requiredIf($isSuccessful), 'date', 'before_or_equal:'.today()->startOfDay()],
            'bid_bond'       => [Rule::requiredIf($isSuccessful), 'integer'],
            'or_number'      => [Rule::requiredIf($isSuccessful), 'string'],
            'approved_date'  => [Rule::requiredIf($isSuccessful), 'date', 'before_or_equal:'.today()->startOfDay()],
            'truck_no'       => ['nullable', 'string'],
            'payment_type'   => [Rule::requiredIf($isSuccessful), 'string'],
            'from'           => [Rule::requiredIf($isPreHauling), 'date', 'after_or_equal:'.today()->startOfDay()],
            'to'             => [Rule::requiredIf($isPreHauling), 'date', 'after_or_equal:from'],
            'haulings'       => ['nullable', 'array'],
            // 'team_leader'    => ['nullable'],
            // 'team_driver'    => ['nullable'],
            // 'safety_officer' => ['nullable'],
            // 'team_mechanic'  => ['nullable'],
            // 'haulers'        => ['nullable', 'array', 'max:12'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.after_or_equal' => 'Starting date should be today or in the future.',
            'to.after_or_equal'   => 'Ending date should be after or equal the starting date.',
        ];
    }

    private function isOfStatus(JobOrderStatus $jobOrderStatus): bool
    {
        $status = $this->enum('status', JobOrderStatus::class);

        return $status === $jobOrderStatus;
    }
}
