<?php

namespace App\Filament\Resources\PondResource\Pages;

use App\Filament\Resources\PondResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPond extends EditRecord
{
    protected static string $resource = PondResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
