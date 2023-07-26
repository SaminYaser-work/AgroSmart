<?php

namespace App\Filament\Resources\AnimalResource\Pages;

use App\Filament\Resources\AnimalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnimals extends ListRecords
{
    protected static string $resource = AnimalResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
