<?php

namespace App\Http\Requests;

use App\Enums\JobOrderServiceType;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreITServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isItService = $this->enum('service_type', JobOrderServiceType::class) === JobOrderServiceType::ITService;

        return [
            'service_type'     => ['required', 'string', Rule::in(JobOrderServiceType::cases())],
            'date_time'        => ['required', 'date'],
            'client'           => ['required', 'string'],
            'address'          => ['required', 'string'],
            'department'       => ['required', 'string'],
            'contact_position' => ['required', 'string'],
            'contact_person'   => ['required', 'string'],
            'contact_no'       => ['required', 'digits:11'],
            'technician_id'    => [Rule::requiredIf($isItService), Rule::exists((new Employee)->getTable(), 'id')],
            'machine_type'     => [Rule::requiredif($isItService), 'string', 'max:255'],
            'model'            => [Rule::requiredif($isItService), 'string', 'max:255'],
            'serial_no'        => [Rule::requiredif($isItService), 'string', 'max:255'],
            'tag_no'           => [Rule::requiredif($isItService), 'string', 'max:255'],
            'machine_problem'  => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'serial_no' => 'The serial number field is required.',
            'tag_no'    => 'The tag number field is required.',
        ];
    }
}
