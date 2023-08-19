<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class CustomerProfileStats extends BaseWidget
{
    public Customer|null $record = null;
    protected int | string | array $columnSpan = 2;
    protected function getCards(): array
    {
        $totalOrders = $this->record->salesOrders()->count();
        $totalSpent = money($this->record->salesOrders()->sum('amount'), 'bdt');

        return [
            Card::make('Total Orders', $totalOrders),
            Card::make('Total Spent', $totalSpent),
        ];
    }
}
