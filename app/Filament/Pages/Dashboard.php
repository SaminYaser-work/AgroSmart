<?php

namespace App\Filament\Pages;

use App\Filament\Widgets;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{

//    protected function getHeaderWidgetsColumns(): int|string|array
//    {
//        return 4;
//    }

    protected function getColumns(): int|string|array
    {
        return 4;
    }

    protected function getWidgets(): array
    {
        return [
            Widgets\DashboardStats::class,
            Widgets\DeliveredOrderChart::class,
            Widgets\OrderPerMonthChart::class,
            Widgets\ExpenseBreakdownChart::class,
            Widgets\AccountingChart::class,
            Widgets\TempLineChart::class,
            Widgets\LatestOrders::class,
            Widgets\LatestPurchases::class,
        ];
    }
}
