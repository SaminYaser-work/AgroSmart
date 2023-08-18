<?php

namespace App\Filament\Resources\CropProjectResource\Pages;

use App\Filament\Resources\CropProjectResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCropProject extends EditRecord
{
    protected static string $resource = CropProjectResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
