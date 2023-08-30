<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SalesOrderResource;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 4;
    protected function getTableQuery(): Builder
    {
        return SalesOrder::query()->latest('order_date');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('customer_name')
                ->getStateUsing(function (SalesOrder $record) {
                    return $record->customer->first_name . ' ' . $record->customer->last_name;
                })
                ->sortable(['customer.first_name'])
                ->searchable(['customer.first_name', 'customer.last_name'])
                ->url(fn($record) => "/customers/" . $record->customer->id),
            Tables\Columns\TextColumn::make('name')
                ->label('Product'),
            Tables\Columns\BadgeColumn::make('type'),
            Tables\Columns\TextColumn::make('order_date')
                ->date(),
            Tables\Columns\IconColumn::make('status')
                ->options([
                    'fas-triangle-exclamation' => fn($state, SalesOrder $record): bool => LatestOrders::isOrderLate($record),
                    'heroicon-o-clock' => fn($state, SalesOrder $record): bool => LatestOrders::isOrderPending($record),
                    'heroicon-o-check-circle' => fn($state, $record): bool => LatestOrders::isOrderDeliveredOnTime($record)
                ])
                ->colors([
                    'success' => fn($state, $record): bool => LatestOrders::isOrderDeliveredOnTime($record),
                    'danger' => fn($state, SalesOrder $record): bool => LatestOrders::isOrderLate($record),
                    'warning' => fn($state, SalesOrder $record): bool => LatestOrders::isOrderPending($record) || LatestOrders::isOrderDeliveredLate($record),
                ])
                ->tooltip(function ($record) {
                    if (LatestOrders::isOrderLate($record)) return 'Pending & Late';
                    if (LatestOrders::isOrderPending($record)) return 'Pending';
                    if (LatestOrders::isOrderDeliveredLate($record)) return 'Delivered Late';
                    if (LatestOrders::isOrderDeliveredOnTime($record)) return 'Delivered on time';
                    return 'Unknown';
                }),
            Tables\Columns\TextColumn::make('quantity'),
            Tables\Columns\TextColumn::make('unit_price')->money('bdt'),
            Tables\Columns\TextColumn::make('amount')->money('bdt')->sortable(),
        ];
    }

    private static function isOrderLate(SalesOrder $record): bool
    {
        if ($record->actual_delivery_date === null) {
            return Carbon::parse($record->expected_delivary_date)->isPast();
        }
        return false;
    }

    private static function isOrderPending(SalesOrder $record): bool
    {
        if ($record->actual_delivery_date === null) {
            return Carbon::parse($record->expected_delivary_date)->isFuture();
        }
        return false;
    }

    private static function isOrderDeliveredOnTime(SalesOrder $record): bool
    {
        return $record->actual_delivery_date !== null;
    }

    private static function isOrderDeliveredLate(SalesOrder $record): bool
    {
        if ($record->actual_delivery_date !== null) {
            return Carbon::parse($record->actual_delivery_date)->lessThan(Carbon::parse($record->expected_delivary_date));
        }
        return false;
    }
}
