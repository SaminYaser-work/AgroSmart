<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'designation',
        'phone_number',
        'start_date',
        'end_date',
        'salary',
        'bonus',
        'over_time_rate',
        'expected_hours',
        'farm_id',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

}
