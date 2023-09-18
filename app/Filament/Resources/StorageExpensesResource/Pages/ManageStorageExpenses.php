<?php

namespace App\Filament\Resources\StorageExpensesResource\Pages;

use App\Filament\Resources\StorageExpensesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStorageExpenses extends ManageRecords
{
    protected static string $resource = StorageExpensesResource::class;

    protected function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StorageExpensesResource\Widgets\MonthlyStorageExpenseBarChart::class,
            StorageExpensesResource\Widgets\StorageExpenseType::class,
            StorageExpensesResource\Widgets\DailyStorageExpenseLineChart::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
