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
        $isSuccessful = $this->isOfStatus(JobOrderStatus::Successful);
        $isPreHauling = $this->isOfStatus(JobOrderStatus::PreHauling);

        return [
            'status'         => ['required', 'string', Rule::in(JobOrderStatus::cases())],
            'payment_date'   => [Rule::requiredIf($isSuccessful), 'date', 'before_or_equal:'.today()->endOfDay()],
            'bid_bond'       => [Rule::requiredIf($isSuccessful), 'integer'],
            'or_number'      => [Rule::requiredIf($isSuccessful), 'string'],
            'approved_date'  => [Rule::requiredIf($isSuccessful), 'date', 'before_or_equal:'.today()->endOfDay()],
            'payment_type'   => [Rule::requiredIf($isSuccessful), 'string'],
            'from'           => [Rule::requiredIf($isPreHauling), 'date', 'after_or_equal:'.today()->startOfDay()],
            'to'             => [Rule::requiredIf($isPreHauling), 'date', 'after_or_equal:from'],
            'haulings'       => ['nullable', 'array'],
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
