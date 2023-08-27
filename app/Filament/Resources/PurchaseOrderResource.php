<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Storage;
use App\Utils\Enums;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\HtmlString;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'fas-cart-shopping';

    protected static ?string $navigationGroup = 'Supply';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required(),
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('order_date')
                    ->required(),
                Forms\Components\DatePicker::make('expected_delivery_date')
                    ->required(),
                Forms\Components\DatePicker::make('actual_delivery_date'),
                Forms\Components\TextInput::make('quantity')
                    ->required(),
                Forms\Components\TextInput::make('unit_price')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required(),
                Forms\Components\TextInput::make('unit')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(function (PurchaseOrder $record) {
                        return $record->name . '&nbsp;<span class="' .
                            Enums::$badgeClasses . '">' . $record->type . '</span><br>' .
                            '<span class="text-xs text-gray-700">' . $record->supplier->name . '</span>';
                    })->html(),
                Tables\Columns\TextColumn::make('farm.name'),
                Tables\Columns\IconColumn::make('status')
                    ->options([
                        'fas-triangle-exclamation' => fn($state, PurchaseOrder $record): bool => PurchaseOrderResource::isOrderLate($record),
                        'heroicon-o-clock' => fn($state, PurchaseOrder $record): bool => PurchaseOrderResource::isOrderPending($record),
                        'heroicon-o-check-circle' => fn($state, $record): bool => PurchaseOrderResource::isOrderDeliveredOnTime($record)
                    ])
                    ->colors([
                        'success' => fn($state, $record): bool => PurchaseOrderResource::isOrderDeliveredOnTime($record),
                        'danger' => fn($state, PurchaseOrder $record): bool => PurchaseOrderResource::isOrderLate($record),
                        'warning' => fn($state, PurchaseOrder $record): bool => PurchaseOrderResource::isOrderPending($record) || PurchaseOrderResource::isOrderDeliveredLate($record),
                    ])
                    ->tooltip(function ($record) {
                        if (PurchaseOrderResource::isOrderLate($record)) return 'Pending & Late';
                        if (PurchaseOrderResource::isOrderPending($record)) return 'Pending';
                        if (PurchaseOrderResource::isOrderDeliveredLate($record)) return 'Delivered Late';
                        if (PurchaseOrderResource::isOrderDeliveredOnTime($record)) return 'Delivered on time';
                        return 'Unknown';
                    }),
                Tables\Columns\TextColumn::make('order_date')
                    ->date(),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date(),
                Tables\Columns\TextColumn::make('actual_delivery_date')
                    ->color(fn(PurchaseOrder $record) => Carbon::parse($record->actual_delivery_date)->isAfter($record->expected_delivery_date) ? 'danger' : '')
                    ->date(),
                Tables\Columns\TextColumn::make('quantity')
                    ->getStateUsing(function (PurchaseOrder $record) {
                        return $record->quantity . ' ' . $record->unit;
                    }),
                Tables\Columns\TextColumn::make('unit_price'),
                Tables\Columns\TextColumn::make('amount')->money('bdt', true)->sortable(),
            ])
            ->defaultSort('order_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(Enums::$SupplierType, Enums::$SupplierType))
            ])
            ->actions([
                Tables\Actions\Action::make('delivered')
                    ->label('Mark as Delivered')
                    ->action(function (PurchaseOrder $record, array $data) {
                        $record->actual_delivery_date = now();
                        $record->save();

                        Notification::make()
                            ->title('Inventory Updated')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\Select::make('storage')
                            ->label(function (PurchaseOrder $record) {
                                return new HtmlString('Select Storage for <b>' . $record->name . '</b> in <b>' . $record->farm->name . '</b> Farm');
                            })
                            ->options(function (PurchaseOrder $record) {
                                return Storage::where('farm_id', $record->farm_id)->get()->mapWithKeys(function ($storage) {
                                    return [$storage->id => $storage->name . ' (' . $storage->type . ')'];
                                });
                            })
                            ->required()
                    ])
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->disabled(fn($record) => $record->actual_delivery_date !== null),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
