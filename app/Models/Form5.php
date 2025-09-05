<?php

namespace App\Models;

use App\Http\Resources\Form5Resource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Form5 extends Model
{
    use HasFactory;

    protected $resourceClass = Form5Resource::class;
    protected $table = 'form5';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'assigned_person',
        'purpose',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function jobOrder(): MorphOne
    {
        return $this->morphOne(JobOrder::class, 'serviceable');
    }

    public function assignedPerson(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_person');
    }

    public function items()
    {
        return $this->hasMany(Form5Item::class);
    }
        public function getItemsAttribute()
{
    return $this->items()->get(); 
}
public function updateItems(array $items)
{
    $this->items()->delete();
    
    foreach ($items as $item) {
        $this->items()->create([
            'item_name' => $item['item_name'],
            'quantity' => $item['quantity'],
        ]);
    }
}
    public function attributesForCorrection(): array
{
    return [
        'assigned_person',
        'purpose',
        'items',
    ];
}

}