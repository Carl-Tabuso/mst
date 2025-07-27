<?php

namespace App\Models;

use App\Enums\HaulingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Form3HaulingChecklist extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function checkAllFields(): void
    {
        $this->update([
            'is_vehicle_inspection_filled' => true,
            'is_uniform_ppe_filled'        => true,
            'is_tools_equipment_filled'    => true,
            'is_certify'                   => true,
        ]);
    }

    public function setHaulingToInProgress(): void
    {
        $this->form3Hauling()->update([
            'status' => HaulingStatus::InProgress,
        ]);
    }

    public function form3Hauling(): BelongsTo
    {
        return $this->belongsTo(Form3Hauling::class);
    }
}
