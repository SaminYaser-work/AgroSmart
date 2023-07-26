<?php

namespace App\Filament\Resources\AnimalResource\Pages;

use App\Filament\Resources\AnimalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAnimal extends CreateRecord
{
    protected static string $resource = AnimalResource::class;
}
