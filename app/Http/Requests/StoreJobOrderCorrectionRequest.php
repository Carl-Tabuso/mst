<?php

namespace App\Http\Requests;

use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Enums\MachineStatus;
use App\Models\Employee;
use App\Models\JobOrderCorrection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobOrderCorrectionRequest extends FormRequest
{
    private array $rules = [
        'date_time'        => ['required', 'date'],
        'client'           => ['required', 'string'],
        'address'          => ['required', 'string'],
        'department'       => ['required', 'string'],
        'contact_position' => ['required', 'string'],
        'contact_person'   => ['required', 'string'],
        'contact_no'       => ['required', 'digits:11'],
        'reason'           => ['required', 'string'],
    ];

    public function authorize(): bool
    {
        return $this->user()->can('create', [JobOrderCorrection::class, $this->ticket]);
    }

    public function rules(): array
    {
        $serviceType = $this->ticket->serviceable_type;

        match ($serviceType) {
            JobOrderServiceType::Form4     => $this->pushWasteManagementRules(),
            JobOrderServiceType::ITService => $this->pushITServiceRules(),
            JobOrderServiceType::Form5     => $this->pushOtherServicesRules(),
        };

        return $this->rules;
    }

    protected function prepareForValidation(): void
    {
        $isWasteManagement = $this->ticket->serviceable_type === JobOrderServiceType::Form4;

        $this->when($isWasteManagement, fn () => $this->merge([
            'bid_bond' => (float) str_replace(',', '', $this->bid_bond),
        ]));
    }

    private function pushWasteManagementRules()
    {
        $wasteManagementRules = [
            'payment_date'  => ['sometimes', 'required', 'date'],
            'or_number'     => ['sometimes', 'required', 'string'],
            'bid_bond'      => ['sometimes', 'required'],
            'payment_type'  => ['sometimes', 'required', 'string'],
            'approved_date' => ['sometimes', 'required', 'date'],
        ];

        $this->rules = array_merge($this->rules, $wasteManagementRules);
    }

    private function pushITServiceRules()
    {
        $this->rules = array_merge($this->rules, [
            'technician'      => ['required', Rule::exists((new Employee)->getTable(), 'id')],
            'machine_type'    => ['required', 'string', 'max:255'],
            'model'           => ['required', 'string', 'max:255'],
            'serial_no'       => ['required', 'string', 'max:255'],
            'tag_no'          => ['required', 'string', 'max:255'],
            'machine_problem' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->ticket->status === JobOrderStatus::ForFinalService) {
            $this->rules = array_merge($this->rules, [
                'initial_service_performed' => ['required', 'string'],
                'recommendation'            => ['required', 'string'],
                'initial_machine_status'    => ['required', 'string', Rule::in(MachineStatus::cases())],
                'report_file'               => ['nullable', Rule::when($this->hasFile('report_file'), ['mimes:pdf,jpg,png,doc,docx', 'max:5120'])],
            ]);
        }

        if ($this->ticket->status === JobOrderStatus::Completed) {
            $this->rules = array_merge($this->rules, [
                'final_service_performed' => ['required', 'string'],
                'parts_replaced'          => ['required', 'string'],
                'remarks'                 => ['required', 'string'],
                'final_machine_status'    => ['required', 'string', Rule::in(MachineStatus::cases())],
            ]);
        }
    }

    private function pushOtherServicesRules()
    {
        //
    }
}
