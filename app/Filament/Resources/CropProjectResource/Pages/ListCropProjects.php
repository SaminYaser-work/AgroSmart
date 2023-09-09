<?php

namespace App\Filament\Resources\CropProjectResource\Pages;

use App\Filament\Resources\CropProjectResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCropProjects extends ListRecords
{
    protected static string $resource = CropProjectResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            CropProjectResource\Widgets\CropStats::class,
            CropProjectResource\Widgets\CurrentCrops::class,
            CropProjectResource\Widgets\CropStages::class
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('dd')->label('Disease Detection')->url(CropProjectResource::getUrl('dd')),
            Actions\CreateAction::make()->label('New Crop Production'),
        ];
    }
}
