<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource;
use App\Filament\Resources\WorkerResource\Widgets\WorkerByFarmChart;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkers extends ListRecords
{
    protected static string $resource = WorkerResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            WorkerByFarmChart::class
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
