<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use App\Utils\Enums;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'fas-truck-field';

    protected static ?string $navigationGroup = 'Supply';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('lead_time')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(array_combine(Enums::$SupplierType, Enums::$SupplierType))
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier')
                    ->getStateUsing(function (Supplier $record) {
                        return $record->name . '<br/>' . '<span class="text-xs">' . $record->address . '</span>';
                    })
                    ->html(),
                Tables\Columns\BadgeColumn::make('type'),
                Tables\Columns\TextColumn::make('phone')
                    ->copyable()
                    ->icon('fas-phone-alt'),
                Tables\Columns\TextColumn::make('email')
                    ->copyable()
                    ->icon('fas-envelope-open-text'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(Enums::$SupplierType, Enums::$SupplierType))
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === '' || $data['value'] === null) {
                            return $query;
                        }
                        $query->where('type', $data['value']);
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
