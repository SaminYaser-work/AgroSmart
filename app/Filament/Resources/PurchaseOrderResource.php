<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'fas-money-bills';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $navigationLabel = 'Sales Orders';
    protected static ?string $modelLabel = 'Sales Order';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'id')
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
                Tables\Columns\TextColumn::make('customer_name')
                    ->getStateUsing(function (PurchaseOrder $record) {
                        return $record->customer->first_name . ' ' . $record->customer->last_name;
                    })
                    ->sortable(['customer.first_name'])
                    ->searchable(['customer.first_name', 'customer.last_name'])
                    ->url(fn($record) => "/customers/" . $record->customer->id),
//                Tables\Columns\TextColumn::make('farm.name'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Product'),
                Tables\Columns\BadgeColumn::make('type'),
                Tables\Columns\TextColumn::make('order_date')
                    ->date(),
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
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('unit_price')->money('bdt'),
                Tables\Columns\TextColumn::make('amount')->money('bdt')->sortable(),
            ])
            ->defaultSort('order_date', 'desc')
            ->filters([
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
            ])
            ->actions([
                Tables\Actions\Action::make('delivered')
                    ->label('Mark as Delivered')
                    ->action(function (PurchaseOrder $record) {
                        $record->actual_delivery_date = now();
                        $record->save();
                    })
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->disabled(fn($record) => $record->actual_delivery_date !== null),
            ])
            ->bulkActions([
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
