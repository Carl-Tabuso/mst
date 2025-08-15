<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ITServiceCorrection extends Model
{
    use HasFactory;

    protected $table = 'it_service_corrections';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'properties'  => 'collection',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'reviewed_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    public function itService(): BelongsTo
    {
        return $this->belongsTo(ITService::class);
    }

    public function jobOrder()
    {
        return $this->hasOneThrough(
            JobOrder::class,
            ITService::class,
            'id',
            'serviceable_id',
            'it_service_id',
            'id'
        )->where('serviceable_type', ITService::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
