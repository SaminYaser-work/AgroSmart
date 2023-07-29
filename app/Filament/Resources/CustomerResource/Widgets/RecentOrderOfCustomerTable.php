<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\Customer;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentOrderOfCustomerTable extends BaseWidget
{

    protected static ?string $heading = 'Recent Orders';

    public Customer|null $record = null;

    protected int | string | array $columnSpan = 2;

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50, 100];
    }

    protected function getTableQuery(): Builder
    {
        return PurchaseOrder::query()->where('customer_id', $this->record->id)->orderBy('order_date', 'desc');
    }

    private static function isOrderLate(PurchaseOrder $record): bool
    {
        if ($record->actual_delivery_date === null) {
            return Carbon::parse($record->expected_delivary_date)->isPast();
        }
        return false;
    }

    private static function isOrderPending(PurchaseOrder $record): bool
    {
        if ($record->actual_delivery_date === null) {
            return Carbon::parse($record->expected_delivary_date)->isFuture();
        }
        return false;
    }

    private static function isOrderDeliveredOnTime(PurchaseOrder $record): bool
    {
        return $record->actual_delivery_date !== null;
    }

    private static function isOrderDeliveredLate(PurchaseOrder $record): bool
    {
        if ($record->actual_delivery_date !== null) {
            return Carbon::parse($record->actual_delivery_date)->lessThan(Carbon::parse($record->expected_delivary_date));
        }
        return false;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Product'),
            Tables\Columns\BadgeColumn::make('type'),
            Tables\Columns\TextColumn::make('order_date')
                ->date(),
            Tables\Columns\IconColumn::make('status')
                ->options([
                    'fas-triangle-exclamation' => fn($state, PurchaseOrder $record): bool => RecentOrderOfCustomerTable::isOrderLate($record),
                    'heroicon-o-clock' => fn($state, PurchaseOrder $record): bool => RecentOrderOfCustomerTable::isOrderPending($record),
                    'heroicon-o-check-circle' => fn($state, $record): bool => RecentOrderOfCustomerTable::isOrderDeliveredOnTime($record)
                ])
                ->colors([
                    'success' => fn($state, $record): bool => RecentOrderOfCustomerTable::isOrderDeliveredOnTime($record),
                    'danger' => fn($state, PurchaseOrder $record): bool => RecentOrderOfCustomerTable::isOrderLate($record),
                    'warning' => fn($state, PurchaseOrder $record): bool => RecentOrderOfCustomerTable::isOrderPending($record) || RecentOrderOfCustomerTable::isOrderDeliveredLate($record),
                ])
                ->tooltip(function ($record) {
                    if (RecentOrderOfCustomerTable::isOrderLate($record)) return 'Pending & Late';
                    if (RecentOrderOfCustomerTable::isOrderPending($record)) return 'Pending';
                    if (RecentOrderOfCustomerTable::isOrderDeliveredLate($record)) return 'Delivered Late';
                    if (RecentOrderOfCustomerTable::isOrderDeliveredOnTime($record)) return 'Delivered on time';
                    return 'Unknown';
                }),
            Tables\Columns\TextColumn::make('quantity'),
            Tables\Columns\TextColumn::make('unit_price')->money('bdt'),
            Tables\Columns\TextColumn::make('amount')->money('bdt')->sortable(),
        ];
    }
}
