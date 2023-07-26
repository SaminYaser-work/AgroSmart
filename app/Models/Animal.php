<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'breed',
        'color',
        'gender',
        'storage_id',
        'farm_id',
    ];

    public function storages(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }

    public function farms(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
