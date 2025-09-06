<?php

namespace App\Http\Requests;

use App\Models\Truck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTruckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit', 'App\\Models\Truck');
    }

    public function rules(): array
    {
        return [
            'model'    => ['sometimes', 'required'],
            'plate_no' => [
                'sometimes',
                'required',
                Rule::unique((new Truck)->getTable(), 'plate_no')
                    ->ignoreModel($this->truck, 'plate_no'),
            ],
        ];
    }
}
