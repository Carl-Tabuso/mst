<?php

namespace App\Http\Requests;

use App\Models\Truck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTruckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Truck::class);
    }

    public function rules(): array
    {
        return [
            'model'    => ['required'],
            'plate_no' => ['required', Rule::unique((new Truck)->getTable(), 'plate_no')],
        ];
    }

    public function messages(): array
    {
        return [
            'plate_no' => 'This plate number has already been taken.',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'added_by' => $this->user()->employee_id,
        ]);
    }
}
