<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
