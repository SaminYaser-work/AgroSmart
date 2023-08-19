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
        "expected_end_date",
        "status",
        "yield",
        "expected_yield",
        "field_id",
        "farm_id"
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
