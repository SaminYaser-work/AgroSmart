<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Filament\Resources\SalesOrderResource;
use App\Models\Customer;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
        return SalesOrder::query()->where('customer_id', $this->record->id)->orderBy('order_date', 'desc');
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

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('type')
                ->options([
                    'Dairy' => 'Dairy',
                    'Crop' => 'Crop',
                    'Fishery' => 'Fishery',
                ]),
            Tables\Filters\Filter::make('actual_delivery_date')
                ->query(function ($query, array $data) {
                    if ($data['isActive']) {
                        $query->whereNotNull('actual_delivery_date');
                    } else {
                        $query->whereNull('actual_delivery_date');
                    }
                })
                ->label('Show only delivered')
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('delivered')
                ->label('Mark as Delivered')
                ->action(function (SalesOrder $record) {
                    $record->actual_delivery_date = now();
                    $record->save();
                })
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->disabled(fn($record) => $record->actual_delivery_date !== null),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('delivered_bulk')
                ->label('Mark Selected as Delivered')
                ->action(function (Collection $records) {
                    $records->each(function ($record) {
                        $record->update([
                            'actual_delivery_date' => Carbon::now()->toDateString()
                        ]);
                    });
                    redirect("/sales-orders");
                })
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->deselectRecordsAfterCompletion()
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Product'),
            Tables\Columns\BadgeColumn::make('type'),
            Tables\Columns\TextColumn::make('order_date')
                ->date()
                ->sortable(),
            Tables\Columns\IconColumn::make('status')
                ->options([
                    'fas-triangle-exclamation' => fn($state, SalesOrder $record): bool => RecentOrderOfCustomerTable::isOrderLate($record),
                    'heroicon-o-clock' => fn($state, SalesOrder $record): bool => RecentOrderOfCustomerTable::isOrderPending($record),
                    'heroicon-o-check-circle' => fn($state, $record): bool => RecentOrderOfCustomerTable::isOrderDeliveredOnTime($record)
                ])
                ->colors([
                    'success' => fn($state, $record): bool => RecentOrderOfCustomerTable::isOrderDeliveredOnTime($record),
                    'danger' => fn($state, SalesOrder $record): bool => RecentOrderOfCustomerTable::isOrderLate($record),
                    'warning' => fn($state, SalesOrder $record): bool => RecentOrderOfCustomerTable::isOrderPending($record) || RecentOrderOfCustomerTable::isOrderDeliveredLate($record),
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
