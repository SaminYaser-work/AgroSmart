<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PondWeeklyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'production',
        'yield',
        'survival_rate',
        'average_weight',
        'average_growth',
        'dissolved_oxygen',
        'water_level',
        'water_temperature',
        'ph',
        'turbidity',
        'ammonia',
        'nitrate',
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
