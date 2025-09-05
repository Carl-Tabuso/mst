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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;

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


    switch ($jobOrder->serviceable_type) {
        case JobOrderServiceType::Form4:
            $jobOrder->load(['serviceable', 'serviceable.form3']);
            break;
        case JobOrderServiceType::Form5:
            $jobOrder->load(['serviceable', 'serviceable.items']);
            break;
        case JobOrderServiceType::ITService:
            $jobOrder->load('serviceable');
            break;
        default:
            $jobOrder->load('serviceable');
    }

    $jobOrder->fill([
        'date_time'        => Carbon::parse($validated->date_time),
        'client'           => $validated->client,
        'address'          => $validated->address,
        'department'       => $validated->department,
        'contact_position' => $validated->contact_position,
        'contact_person'   => $validated->contact_person,
        'contact_no'       => $validated->contact_no,
    ]);

    $updateableModels = [$jobOrder];
    $correctionData = ['properties' => ['before' => [], 'after' => []]];


    if ($this->canUpdateProposal($jobOrder->status)) {

        if ($jobOrder->serviceable_type === JobOrderServiceType::Form4) {
            
            $jobOrder->serviceable->fill([
                'payment_date' => Carbon::parse($validated->payment_date),
                'or_number'    => $validated->or_number,
                'bid_bond'     => $validated->bid_bond,
            ]);

            $jobOrder->serviceable->form3->fill([
                'payment_type'  => $validated->payment_type,
                'approved_date' => Carbon::parse($validated->approved_date),
            ]);

            array_push($updateableModels, $jobOrder->serviceable, $jobOrder->serviceable->form3);
        }
        elseif ($jobOrder->serviceable_type === JobOrderServiceType::Form5) {
            
            $form5 = $jobOrder->serviceable;
            
            $originalValues = [
                'assigned_person' => $form5->assigned_person,
                'purpose' => $form5->purpose,
                'items' => $form5->items->toArray()        ];
            
            
            $form5->fill([
                'assigned_person' => $validated->assigned_person ?? $form5->assigned_person,
                'purpose' => $validated->purpose ?? $form5->purpose,
            ]);
            
            $newItems = isset($validated->items) ? $validated->items : $form5->items->toArray();

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
        elseif ($jobOrder->serviceable_type === JobOrderServiceType::ITService) {
            $itService = $jobOrder->serviceable;
            array_push($updateableModels, $itService);
        }
    } else {
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

    $correctionData['reason'] = $validated->reason;

    $correction = $jobOrder->corrections()->create($correctionData);
    
}    public function updateJobOrderCorrection(array $data, JobOrderCorrection $correction)
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
                JobOrderServiceType::ITService => $this->updateItService($newValues),
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

    private function updateItService(array $data)
    {
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