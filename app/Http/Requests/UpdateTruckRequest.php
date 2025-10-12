<?php

namespace App\Http\Requests;

use App\Models\Truck;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTruckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit', $this->truck);
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

    protected function failedAuthorization(): never
    {
        throw new AuthorizationException(__('responses.truck.unauthorized'));
    }
}
