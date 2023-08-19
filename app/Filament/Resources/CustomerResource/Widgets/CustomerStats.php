<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class CustomerStats extends BaseWidget
{
    protected function getCards(): array
    {

        $totalCustomers = Customer::count();
        $avgOrdersPerCustomer = Customer::withCount('salesOrders')->get()->avg('sales_orders_count');

        return [
            Card::make('Total Customers', $totalCustomers),
            Card::make('Avg Orders Per Customer', $avgOrdersPerCustomer),
        ];
    }
}
