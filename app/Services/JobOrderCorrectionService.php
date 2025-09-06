<?php

namespace App\Services;

use App\Enums\JobOrderCorrectionRequestStatus;
use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Filters\JobOrderCorrection\FilterDistinctByJobOrderId;
use App\Filters\JobOrderCorrection\FilterOnlyCreated;
use App\Filters\JobOrderCorrection\FilterStatuses;
use App\Filters\JobOrderCorrection\SearchDetails;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\Storage;

class JobOrderCorrectionService
{
    public function getAllJobOrderCorrections(?int $perPage = 10, ?string $search = '', ?array $filters = []): ResourceCollection
    {
        $user = request()->user();

        $pipes = [
            // new FilterDistinctByJobOrderId,
            new FilterOnlyCreated($user),
            new FilterStatuses($filters),
            new SearchDetails($search),
        ];

        return Pipeline::send(JobOrderCorrection::query())
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage) {
                return $query
                    ->with('jobOrder.creator.account:avatar')
                    ->latest()
                    ->paginate($perPage)
                    ->withQueryString()
                    ->toResourceCollection();
            });
    }

    public function storeJobOrderCorrection(array $data, JobOrder $jobOrder): void
    {
        $validated = (object) $data;

        match ($jobOrder->serviceable_type) {
            JobOrderServiceType::Form4     => $this->storeWasteManagement($jobOrder, $validated),
            JobOrderServiceType::ITService => $this->storeITService($jobOrder, $validated),
            JobOrderServiceType::Form5     => $this->storeOtherService($jobOrder, $validated),
        };
    }

    private function storeWasteManagement(JobOrder $jobOrder, object $data)
    {
        $jobOrder->load(['serviceable', 'serviceable.form3']);

        $jobOrder->fill([
            'date_time'        => Carbon::parse($data->date_time),
            'client'           => $data->client,
            'address'          => $data->address,
            'department'       => $data->department,
            'contact_position' => $data->contact_position,
            'contact_person'   => $data->contact_person,
            'contact_no'       => $data->contact_no,
        ]);

        $updateableModels = [$jobOrder];
        $correctionData = ['properties' => ['before' => [], 'after' => []]];

        if ($this->canUpdateProposal($jobOrder->status)) {
            $jobOrder->serviceable->fill([
                'payment_date' => Carbon::parse($data->payment_date),
                'or_number'    => $data->or_number,
                'bid_bond'     => $data->bid_bond,
            ]);

            $jobOrder->serviceable->form3->fill([
                'payment_type'  => $data->payment_type,
                'approved_date' => Carbon::parse($data->approved_date),
            ]);

            array_push($updateableModels, $jobOrder->serviceable, $jobOrder->serviceable->form3);
        }

        $mappedData = [];
        foreach ($updateableModels as $model) {
            foreach ($model->getDirty() as $key => $value) {
                $mappedData['properties']['before'][$key] = $model->getOriginal($key);
                $mappedData['properties']['after'][$key]  = $value;
            }
        }

        $mappedData['reason'] = $data->reason;

        $jobOrder->corrections()->create($mappedData);
    }

    private function storeITService(JobOrder $jobOrder, object $data)
    {
        $jobOrder->load('serviceable');

        $jobOrder->serviceable->fill([
            'machine_type'    => $data->machine_type,
            'model'           => $data->model,
            'technician_id'   => $data->technician,
            'serial_no'       => $data->serial_no,
            'tag_no'          => $data->tag_no,
            'machine_problem' => $data?->machine_problem,
        ]);

        $updateableModels = [
            $jobOrder,
            $jobOrder->serviceable,
        ];

        $forFinalService = $jobOrder->status === JobOrderStatus::ForFinalService;
        $completed       = $jobOrder->status === JobOrderStatus::Completed;

        if ($forFinalService || $completed) {
            $initialOnsite = $jobOrder->serviceable->initialOnsiteReport;

            $attributes = [
                'service_performed' => $data->initial_service_performed,
                'recommendation'    => $data->recommendation,
                'machine_status'    => $data->initial_machine_status,
                ...(! is_string($data->report_file) ? [
                    'file_name' => $data?->report_file?->getClientOriginalName(),
                    'file_hash' => $data?->report_file?->hashName(),
                ] : []),
            ];

            $initialOnsite->fill($attributes);

            $updateableModels[] = $initialOnsite;
        }

        if ($completed) {
            $finalOnsite = $jobOrder->serviceable->finalOnsiteReport;

            $finalOnsite->fill([
                'service_performed' => $data->final_service_performed,
                'parts_replaced'    => $data->parts_replaced,
                'remarks'           => $data->remarks,
                'machine_status'    => $data->machine_status,
            ]);

            $updateableModels[] = $finalOnsite;
        }

        $mappedData            = [];
        $conflictingAttributes = ['service_performed', 'machine_status'];
        foreach ($updateableModels as $model) {
            foreach ($model->getDirty() as $key => $value) {
                if (in_array($key, $conflictingAttributes)) {
                    $alias = $model->getMorphClass();
                    $key   = "{$alias}.{$key}";
                }

                $mappedData['properties']['before'][$key] = $model->getOriginal($key);
                $mappedData['properties']['after'][$key]  = $value;
            }
        }

        $mappedData['reason'] = $data->reason;

        $jobOrder->corrections()->create($mappedData);

        if ($data?->report_file instanceof UploadedFile) {
            Storage::put('it_services/temp', $data->report_file);
        }
    }

    private function storeOtherService(JobOrder $jobOrder, object $data)
    {
        $jobOrder->load(['serviceable', 'serviceable.items']);

        $jobOrder->fill([
            'date_time'        => Carbon::parse($data->date_time),
            'client'           => $data->client,
            'address'          => $data->address,
            'department'       => $data->department,
            'contact_position' => $data->contact_position,
            'contact_person'   => $data->contact_person,
            'contact_no'       => $data->contact_no,
        ]);

        $updateableModels = [$jobOrder];
        $correctionData = ['properties' => ['before' => [], 'after' => []]];

        if ($this->canUpdateProposal($jobOrder->status)) {
            $form5 = $jobOrder->serviceable;
            
            $originalValues = [
                'assigned_person' => $form5->assigned_person,
                'purpose' => $form5->purpose,
                'items' => $form5->items->toArray()
            ];
            
            $form5->fill([
                'assigned_person' => $data->assigned_person ?? $form5->assigned_person,
                'purpose' => $data->purpose ?? $form5->purpose,
            ]);
            
            $newItems = isset($data->items) ? $data->items : $form5->items->toArray();

            $newValues = [
                'assigned_person' => $form5->assigned_person,
                'purpose' => $form5->purpose,
                'items' => $newItems
            ];

            foreach ($originalValues as $key => $originalValue) {
                $newValue = $newValues[$key];
                
                if ($key === 'items') {
                    $originalJson = json_encode($originalValue);
                    $newJson = json_encode($newValue);
                    
                    if ($originalJson !== $newJson) {
                        $correctionData['properties']['before']['serviceable'][$key] = $originalValue;
                        $correctionData['properties']['after']['serviceable'][$key] = $newValue;
                    }
                } else {
                    if ($originalValue != $newValue) {
                        $correctionData['properties']['before']['serviceable'][$key] = $originalValue;
                        $correctionData['properties']['after']['serviceable'][$key] = $newValue;
                    }
                }
            }

            array_push($updateableModels, $form5);
        }

        foreach ($updateableModels as $model) {
            $dirty = $model->getDirty();
            
            foreach ($dirty as $key => $value) {
                if ($model === $jobOrder->serviceable && 
                    $jobOrder->serviceable_type === JobOrderServiceType::Form5 &&
                    isset($correctionData['properties']['before']['serviceable'][$key])) {
                    continue;
                }
                
                $originalValue = $model->getOriginal($key);
                $correctionData['properties']['before'][$key] = $originalValue;
                $correctionData['properties']['after'][$key] = $value;
            }
        }

        $correctionData['reason'] = $data->reason;

        $jobOrder->corrections()->create($correctionData);
    }

    public function updateJobOrderCorrection(array $data, JobOrderCorrection $correction)
    {
        return DB::transaction(function () use ($data, $correction) {
            $status = JobOrderCorrectionRequestStatus::from($data['status']);

            $serviceType = $correction->jobOrder->serviceable_type;

            $correction->status = $status;
            $correction->jobOrder->increment('error_count');

            if ($status !== JobOrderCorrectionRequestStatus::Approved) {
                return $correction->save();
            }

            $newValues = $data['new_values'];

            match ($serviceType) {
                JobOrderServiceType::Form4     => $this->updateWasteManagement($newValues, $correction->jobOrder),
                JobOrderServiceType::ITService => $this->updateItService($newValues, $correction->jobOrder),
                JobOrderServiceType::Form5     => $this->updateOtherService($newValues, $correction->jobOrder),
            };

            $correction->approved_at = now();

            return $correction->save();
        });
    }

    private function updateWasteManagement(array $data, JobOrder $jobOrder)
    {
        foreach ($jobOrder->attributesForCorrection() as $key) {
            if (array_key_exists($key, $data)) {
                $jobOrder->fill([$key => $data[$key]]);
            }
        }
        $jobOrder->save();

        if (! $this->canUpdateProposal($jobOrder->status)) {
            return;
        }

        $form4 = $jobOrder->serviceable;
        foreach ($form4->attributesForCorrection() as $key) {
            if (array_key_exists($key, $data)) {
                $form4->fill([$key => $data[$key]]);
            }
        }
        $form4->save();

        $form3 = $jobOrder->serviceable->form3;
        foreach ($form3->attributesForCorrection() as $key) {
            if (array_key_exists($key, $data)) {
                $form3->fill([$key => $data[$key]]);
            }
        }
        $form3->save();
    }

    private function canUpdateProposal(JobOrderStatus $status): bool
    {
        return in_array($status, JobOrderStatus::getCanRequestCorrectionStages());
    }

    private function updateItService(array $data, JobOrder $jobOrder)
    {
        // IT Service update logic
    }

    private function updateOtherService(array $data, JobOrder $jobOrder)
    {
        foreach ($jobOrder->attributesForCorrection() as $key) {
            if (array_key_exists($key, $data)) {
                $jobOrder->fill([$key => $data[$key]]);
            }
        }
        $jobOrder->save();

        if (! $this->canUpdateProposal($jobOrder->status)) {
            return;
        }

        $form5 = $jobOrder->serviceable;
        
        if (isset($data['serviceable'])) {
            foreach ($data['serviceable'] as $key => $value) {
                if ($key !== 'items') { 
                    $form5->fill([$key => $value]);
                }
            }
        }
        
        if (isset($data['serviceable']['items'])) {
            $form5->items()->delete();
            
            foreach ($data['serviceable']['items'] as $itemData) {
                $form5->items()->create([
                    'item_name' => $itemData['item_name'],
                    'quantity' => $itemData['quantity'],
                ]);
            }
        }
        
        $form5->save();
    }

    public function deleteJobOrderCorrection(JobOrderCorrection $correction)
    {
        return $correction->delete();
    }

    public function batchDeleteJobOrderCorrections(array $correctionIds)
    {
        return DB::transaction(fn () => JobOrderCorrection::destroy($correctionIds));
    }
}