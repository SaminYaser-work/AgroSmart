<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'area',
        'name',
        'soil_type',
        'status',
        'farm_id',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
