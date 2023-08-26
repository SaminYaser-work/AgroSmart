<?php

namespace App\Filament\Resources\FishProductionResource\Pages;

use App\Filament\Resources\FishProductionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFishProductions extends ManageRecords
{
    protected static string $resource = FishProductionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
