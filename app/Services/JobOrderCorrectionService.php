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

        if ($this->canUpdateProposal($jobOrder->status)) {
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

        $data = [];

        foreach ($updateableModels as $model) {
            foreach ($model->getDirty() as $key => $value) {
                $data['properties']['before'][$key] = $model->getOriginal($key);
                $data['properties']['after'][$key]  = $value;
            }
        }

        $data['reason'] = $validated->reason;

        $jobOrder->corrections()->create($data);
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
                JobOrderServiceType::ITService => $this->updateItService($newValues),
                JobOrderServiceType::Form5     => $this->updateOtherService($newValues),
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
        //
    }

    private function updateOtherService(array $data)
    {
        //
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
