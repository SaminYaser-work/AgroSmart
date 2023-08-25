<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pond extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'pond_type',
        'water_type',
        'fish',
        'size',
        'farm_id',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
