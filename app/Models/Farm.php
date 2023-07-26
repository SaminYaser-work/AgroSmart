<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
    ];

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    public function storages(): HasMany
    {
        return $this->hasMany(Storage::class);
    }

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    public function animalProductions(): HasMany
    {
        return $this->hasMany(AnimalProduction::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }
}
