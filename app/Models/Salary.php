<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'farm_id',
        'month',
        'year',
        'base',
        'overtime',
        'penalty',
        'total',
        'bonus',
        'paid'
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
