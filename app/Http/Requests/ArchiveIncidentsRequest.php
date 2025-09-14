<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArchiveIncidentsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'exists:incidents,id',
        ];
    }
}