<?php

namespace App\Http\Requests;

use App\Enums\MachineStatus;
use App\Models\FinalOnsiteReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinalOnsiteReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [FinalOnsiteReport::class, $this->iTService]);
    }

    public function rules(): array
    {
        return [
            'service_performed' => ['required', 'string'],
            'parts_replaced'    => ['required', 'string'],
            'remarks'           => ['required', 'string'],
            'machine_status'    => ['required', 'string', Rule::in(MachineStatus::cases())],
        ];
    }
}
