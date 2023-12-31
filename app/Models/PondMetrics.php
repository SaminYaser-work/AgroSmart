<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PondMetrics extends Model
{
    use HasFactory;

    protected $fillable = [
        'water_temperature',
        'ph',
        'turbidity',
        'pond_id',
        'farm_id',
    ];

    public function pond(): BelongsTo
    {
        return $this->belongsTo(Pond::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
