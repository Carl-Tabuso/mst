<?php

namespace App\Http\Requests;

use App\Models\ITService;
use App\Models\JobOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInitialOnsiteReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'job_order_id'      => ['required', Rule::exists((new JobOrder)->getTable(), 'id')],
            // 'it_service_id'     => ['required', Rule::exists((new ITService)->getTable(), 'id')],
            'service_performed' => ['required', 'string'],
            'recommendation'    => ['required', 'string'],
            'machine_status'    => ['required', 'string'],
            'report_file'       => ['nullable', 'file', 'mimes:pdf,jpg,png,doc,docx', 'max:5120'],
        ];
    }
}
