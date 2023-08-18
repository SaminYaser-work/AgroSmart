<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropProject extends Model
{
    use HasFactory;

    protected $fillable = [
        "crop_name",
        "start_date",
        "end_date",
        "status",
        "yield",
        "field_id",
        "farm_id"
    ];

    public function fields(): BelongsTo
    {
        return $this->belongsTo('fields');
    }

    public function farms(): BelongsTo
    {
        return $this->belongsTo('farms');
    }
}
