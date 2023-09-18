<?php

namespace App\Filament\Resources\FishExpensesResource\Pages;

use App\Filament\Resources\FishExpensesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFishExpenses extends ManageRecords
{
    protected static string $resource = FishExpensesResource::class;

    protected function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FishExpensesResource\Widgets\MonthlyFishExpenseBarChart::class,
            FishExpensesResource\Widgets\FishExpenseType::class,
            FishExpensesResource\Widgets\DailyFishExpenseLineChart::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
