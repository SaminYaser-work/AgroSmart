<?php

namespace App\Filament\Resources\AnimalExpenseResource\Pages;

use App\Filament\Resources\AnimalExpenseResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnimalExpense extends EditRecord
{
    protected static string $resource = AnimalExpenseResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
