<?php

namespace App\Filament\Resources\AnimalExpenseResource\Pages;

use App\Filament\Resources\AnimalExpenseResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnimalExpenses extends ListRecords
{
    protected static string $resource = AnimalExpenseResource::class;

    protected function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AnimalExpenseResource\Widgets\DailyExpenseLineChart::class,
            AnimalExpenseResource\Widgets\ExpenseType::class
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
