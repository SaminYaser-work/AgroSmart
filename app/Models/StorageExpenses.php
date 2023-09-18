<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorageExpenses extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
