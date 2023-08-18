<?php

namespace App\Filament\Resources\CropProjectResource\Pages;

use App\Filament\Resources\CropProjectResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCropProjects extends ListRecords
{
    protected static string $resource = CropProjectResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
