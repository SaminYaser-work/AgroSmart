<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorageExpensesResource\Pages;
use App\Filament\Resources\StorageExpensesResource\RelationManagers;
use App\Models\StorageExpenses;
use App\Utils\Enums;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StorageExpensesResource extends Resource
{
    protected static ?string $model = StorageExpenses::class;

    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Expenses';
    protected static ?string $navigationIcon = 'fas-receipt';
    protected static ?string $modelLabel = 'Storage Expenses';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('storage_id')
                    ->relationship('storage', 'name')
                    ->required(),
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(array_combine(Enums::$StorageExpenseType, Enums::$StorageExpenseType))
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date(),
                Tables\Columns\TextColumn::make('storage.name'),
                Tables\Columns\TextColumn::make('farm.name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('amount')->money('BDT', true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStorageExpenses::route('/'),
        ];
    }
}
