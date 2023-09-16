<?php

namespace App\Filament\Resources\AnimalExpenseResource\Pages;

use App\Filament\Resources\AnimalExpenseResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAnimalExpense extends CreateRecord
{
    protected static string $resource = AnimalExpenseResource::class;
}
