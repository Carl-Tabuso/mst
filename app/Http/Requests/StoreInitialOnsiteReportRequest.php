<?php

namespace App\Http\Requests;

use App\Models\InitialOnsiteReport;
use Illuminate\Foundation\Http\FormRequest;

class StoreInitialOnsiteReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [InitialOnsiteReport::class, $this->iTService]);
    }

    public function rules(): array
    {
        return [
            'service_performed' => ['required', 'string'],
            'recommendation'    => ['required', 'string'],
            'machine_status'    => ['required', 'string'],
            'report_file'       => ['nullable', 'file', 'mimes:pdf,jpg,png,doc,docx', 'max:5120'],
        ];
    }
}
