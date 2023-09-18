<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Utils\Enums;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPurchases extends BaseWidget
{
    protected int | string | array $columnSpan = 4;
    protected function getTableQuery(): Builder
    {
        return PurchaseOrder::query()->latest('order_date');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->getStateUsing(function (PurchaseOrder $record) {
                    return $record->name . '&nbsp;<span class="' .
                        Enums::$badgeClasses . '">' . $record->type . '</span><br>' .
                        '<span class="text-xs text-gray-700">' . $record->supplier->name . '</span>';
                })->html(),
            Tables\Columns\TextColumn::make('farm.name'),
            Tables\Columns\IconColumn::make('status')
                ->options([
                    'heroicon-o-check-circle' => fn($state, $record): bool => LatestPurchases::isOrderDeliveredOnTime($record) || LatestPurchases::isOrderDeliveredLate($record),
                    'fas-triangle-exclamation' => fn($state, PurchaseOrder $record): bool => LatestPurchases::isOrderLate($record),
                    'heroicon-o-clock' => fn($state, PurchaseOrder $record): bool => LatestPurchases::isOrderPending($record),
                ])
                ->colors([
                    'success' => fn($state, $record): bool => LatestPurchases::isOrderDeliveredOnTime($record),
                    'danger' => fn($state, PurchaseOrder $record): bool => LatestPurchases::isOrderLate($record),
                    'secondary' => fn($state, PurchaseOrder $record): bool => LatestPurchases::isOrderPending($record),
                    'warning' => fn($state, PurchaseOrder $record): bool => LatestPurchases::isOrderDeliveredLate($record),
                ])
                ->tooltip(function ($record) {
                    if (LatestPurchases::isOrderLate($record)) return 'Pending & Late';
                    if (LatestPurchases::isOrderPending($record)) return 'Pending';
                    if (LatestPurchases::isOrderDeliveredLate($record)) return 'Delivered Late';
                    if (LatestPurchases::isOrderDeliveredOnTime($record)) return 'Delivered on time';
                    return 'Unknown';
                }),
            Tables\Columns\TextColumn::make('order_date')
                ->alignCenter()
                ->date(),
//            Tables\Columns\TextColumn::make('expected_delivery_date')
//                ->alignCenter()
//                ->date(),
            Tables\Columns\TextColumn::make('actual_delivery_date')
                ->label('Delivery Date')
                ->color(fn(PurchaseOrder $record) => Carbon::parse($record->actual_delivery_date)->isAfter($record->expected_delivery_date) ? 'danger' : '')
                ->placeholder('--')
                ->alignCenter()
                ->date(),
            Tables\Columns\TextColumn::make('quantity')
                ->getStateUsing(function (PurchaseOrder $record) {
                    return $record->quantity . ' ' . $record->unit;
                }),
            Tables\Columns\TextColumn::make('unit_price'),
            Tables\Columns\TextColumn::make('amount')->money('bdt', true)->sortable(),
        ];
    }

    private static function isOrderLate(PurchaseOrder $record): bool
    {
        if ($record->actual_delivery_date === null) {
            return Carbon::parse($record->expected_delivery_date)->isPast();
        }
        return false;
    }

    private static function isOrderPending(PurchaseOrder $record): bool
    {
        if ($record->actual_delivery_date === null) {
            return Carbon::parse($record->expected_delivery_date)->isFuture();
        }
        return false;
    }

    private static function isOrderDeliveredOnTime(PurchaseOrder $record): bool
    {
        return $record->actual_delivery_date !== null && Carbon::parse($record->actual_delivery_date)->lessThanOrEqualTo(Carbon::parse($record->expected_delivery_date));
    }

    private static function isOrderDeliveredLate(PurchaseOrder $record): bool
    {
        if ($record->actual_delivery_date !== null) {
            return Carbon::parse($record->actual_delivery_date)->greaterThan(Carbon::parse($record->expected_delivery_date));
        }
        return false;
    }
}
