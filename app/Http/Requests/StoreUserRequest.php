<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'role' => 'required|string|in:frontliner,dispatcher,team leader,head frontliner,safety officer,human resource,consultant,regular,it admin'
        ];
    }
}
