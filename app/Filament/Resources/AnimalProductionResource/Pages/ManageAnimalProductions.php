<?php

namespace App\Filament\Resources\AnimalProductionResource\Pages;

use App\Filament\Resources\AnimalProductionResource;
use App\Filament\Resources\AnimalProductionResource\Widgets\MilkProductionLineChart;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAnimalProductions extends ManageRecords
{
    protected static string $resource = AnimalProductionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            AnimalProductionResource\Widgets\MilkProductionByFarm::class,
            AnimalProductionResource\Widgets\MilkProductionComparisonChart::class,
            MilkProductionLineChart::class,
        ];
    }

    protected function getHeaderWidgetsColumns(): int|string|array
    {
        return 4;
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add New Live Stock Production'),
        ];
    }
}
