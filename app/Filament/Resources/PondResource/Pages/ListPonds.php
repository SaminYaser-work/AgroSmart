<?php

namespace App\Filament\Resources\PondResource\Pages;

use App\Filament\Resources\PondResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPonds extends ListRecords
{
    protected static string $resource = PondResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            PondResource\Widgets\PondStats::class,
            PondResource\Widgets\PondsPerFishTypeChart::class,
            PondResource\Widgets\WaterTypeChart::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
