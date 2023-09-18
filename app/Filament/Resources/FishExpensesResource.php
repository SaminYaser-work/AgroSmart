<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FishExpensesResource\Pages;
use App\Filament\Resources\FishExpensesResource\RelationManagers;
use App\Models\FishExpenses;
use App\Utils\Enums;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class FishExpensesResource extends Resource
{
    protected static ?string $model = FishExpenses::class;

    protected static ?string $navigationGroup = 'Fishery';
    protected static ?string $navigationLabel = 'Expenses';
    protected static ?string $navigationIcon = 'fas-receipt';
    protected static ?string $modelLabel = 'Aquaculture Expenses';
    protected static ?int $navigationSort = 5;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->label('Farm')
                    ->required(),
                Forms\Components\Select::make('pond_id')
                    ->relationship('pond', 'name')
                    ->label('Pond')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(array_combine(Enums::$FishExpenseType, Enums::$FishExpenseType))
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
                Tables\Columns\TextColumn::make('farm.name'),
                Tables\Columns\TextColumn::make('pond.name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('amount')->money('BDT', true)
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
            'index' => Pages\ManageFishExpenses::route('/'),
        ];
    }
}
