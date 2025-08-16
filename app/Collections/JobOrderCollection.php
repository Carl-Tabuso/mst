<?php

namespace App\Collections;

use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Collection;

class JobOrderCollection extends Collection
{
    public function groupByServiceType(): JobOrderCollection
    {
        return $this->groupBy(fn (JobOrder $jobOrder) => $jobOrder->serviceable_type);
    }
}
