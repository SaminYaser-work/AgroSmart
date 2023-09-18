<?php

namespace App\Filament\Resources\FarmingExpensesResource\Pages;

use App\Filament\Resources\FarmingExpensesResource;
use App\Filament\Resources\FarmingExpensesResource\Widgets\DailyFarmingExpenseLineChart;
use App\Filament\Resources\FarmingExpensesResource\Widgets\FarmingExpenseType;
use App\Filament\Resources\FarmingExpensesResource\Widgets\MonthlyFarmingExpenseBarChart;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFarmingExpenses extends ManageRecords
{
    protected static string $resource = FarmingExpensesResource::class;

    protected function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MonthlyFarmingExpenseBarChart::class,
            FarmingExpenseType::class,
            DailyFarmingExpenseLineChart::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
