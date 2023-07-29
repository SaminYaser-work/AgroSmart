<?php

namespace App\Filament\Resources\PurchaseOrderResource\Widgets;

use App\Models\PurchaseOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TotalPendingOrders extends BaseWidget
{
    protected function getCards(): array
    {

        $totalDeliveredOnTime = PurchaseOrder::where('actual_delivery_date', '!=', null)
            ->whereColumn('actual_delivery_date', '<=', 'expected_delivery_date')
            ->count();

        $totalDeliveredLate = PurchaseOrder::where('actual_delivery_date', '!=', null)
            ->whereColumn('actual_delivery_date', '>', 'expected_delivery_date')
            ->count();

        $totalPendingOrders = PurchaseOrder::where('actual_delivery_date', null)
            ->where('expected_delivery_date', '>=', now())
            ->count();

        $totalPendingLateOrders = PurchaseOrder::where('actual_delivery_date', null)
            ->where('expected_delivery_date', '<', now())
            ->count();

        return [
            Card::make('', $totalDeliveredOnTime)
                ->description('Orders Delivered On Time')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            Card::make('', $totalPendingOrders)
                ->description('Pending Orders')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('warning'),
            Card::make('', $totalDeliveredLate)
                ->description('Orders Delivered Late')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('warning'),
            Card::make('', $totalPendingLateOrders)
                ->description('Pending Late Orders')
                ->color('danger')
                ->descriptionIcon('fas-triangle-exclamation'),
        ];
    }
}
