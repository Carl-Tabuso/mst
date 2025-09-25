<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => 'required|exists:employees,id|unique:users,employee_id',
            'email'       => 'required|email|unique:users,email',
            'role'        => [
                'required',
                'string',
                Rule::in(array_column(UserRole::cases(), 'value')),
            ],
        ];
    }
}
