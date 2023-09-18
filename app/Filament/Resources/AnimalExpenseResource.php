<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnimalExpenseResource\Pages;
use App\Filament\Resources\AnimalExpenseResource\RelationManagers;
use App\Models\AnimalExpense;
use App\Utils\Enums;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnimalExpenseResource extends Resource
{
    protected static ?string $model = AnimalExpense::class;

    protected static ?string $navigationGroup = 'Livestock';
    protected static ?string $navigationLabel = 'Expenses';

    protected static ?string $navigationIcon = 'fas-receipt';

    protected static ?string $modelLabel = 'Livestock Expense';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('animal_id')
                    ->relationship('animal', 'name')
                    ->required(),
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->required(),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->reactive()
                    ->afterStateUpdated(function ($state, \Closure $set) {
                        $date = Carbon::parse($state);
                        $set('day', $date->copy()->format('d'));
                        $set('month', $date->copy()->format('m'));
                        $set('year', $date->copy()->format('Y'));
                    })
                    ->required(),
                Forms\Components\TextInput::make('day')
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('month')
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->disabled()
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
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('animal.name'),
                Tables\Columns\TextColumn::make('farm.name'),
                Tables\Columns\BadgeColumn::make('type'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('BDT', true)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('farm_id')
                    ->relationship('farm', 'name')
                    ->label('Farm'),
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(Enums::$AnimalExpenseType, Enums::$AnimalExpenseType))
                    ->label('Expense Type')
            ])
            ->defaultSort('date', 'desc')
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
            'index' => Pages\ListAnimalExpenses::route('/'),
            'create' => Pages\CreateAnimalExpense::route('/create'),
            'edit' => Pages\EditAnimalExpense::route('/{record}/edit'),
        ];
    }
}
