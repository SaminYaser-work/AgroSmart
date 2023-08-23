<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'buying_price',
        'yearly_depreciation',
        'farm_id',
        'supplier_id',
        'purchase_order_id',
        'crop_project_id',
        'storage_id',
        'pond_id',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase_order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function crop_project(): BelongsTo
    {
        return $this->belongsTo(CropProject::class);
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }

    public function pond(): BelongsTo
    {
        return $this->belongsTo(Pond::class);
    }
}
