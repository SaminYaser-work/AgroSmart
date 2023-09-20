<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use function Illuminate\Events\queueable;

class FishExpenses extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function pond(): BelongsTo
    {
        return $this->belongsTo(Pond::class);
    }
}
