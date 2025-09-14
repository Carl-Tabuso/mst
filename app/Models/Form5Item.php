<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form5Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'form5_id',
        'item_name',
        'quantity',
    ];

    public function form5()
    {
        return $this->belongsTo(Form5::class);
    }
}
