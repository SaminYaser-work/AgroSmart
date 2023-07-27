<?php

namespace App\Filament\Resources\FieldResource\Pages;

use App\Filament\Resources\FieldResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFields extends ManageRecords
{
    protected static string $resource = FieldResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
