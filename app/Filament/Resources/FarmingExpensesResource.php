<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FarmingExpensesResource\Pages;
use App\Filament\Resources\FarmingExpensesResource\RelationManagers;
use App\Models\FarmingExpenses;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FarmingExpensesResource extends Resource
{
    protected static ?string $model = FarmingExpenses::class;

    protected static ?string $navigationGroup = 'Crop';
    protected static ?string $navigationLabel = 'Expenses';
    protected static ?string $navigationIcon = 'fas-receipt';
    protected static ?string $modelLabel = 'Crop Expense';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('field_id')
                    ->relationship('field', 'name')
                    ->required(),
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->required(),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('field.name'),
                Tables\Columns\TextColumn::make('farm.name'),
                Tables\Columns\BadgeColumn::make('type'),
                Tables\Columns\TextColumn::make('date')
                    ->date(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('BDT', true)
                    ->sortable(),
            ])
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
            'index' => Pages\ManageFarmingExpenses::route('/'),
        ];
    }
}
