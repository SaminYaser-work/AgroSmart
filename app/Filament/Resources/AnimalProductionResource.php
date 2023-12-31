<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnimalProductionResource\Pages;
use App\Filament\Resources\AnimalProductionResource\RelationManagers;
use App\Models\AnimalProduction;
use App\Utils\Enums;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class AnimalProductionResource extends Resource
{
    protected static ?string $model = AnimalProduction::class;

    protected static ?string $navigationIcon = 'fas-glass-water-droplet';
    protected static ?string $navigationGroup = 'Livestock';
    protected static ?string $navigationLabel = 'Production';

    protected static ?string $modelLabel = 'Dairy Production';

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
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
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
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->label('Date'),
                Tables\Columns\TextColumn::make('animal')
                    ->getStateUsing(function (AnimalProduction $record) {
                        return $record->animal->name . '&nbsp;<span class="'. Enums::$badgeClasses .'">' . $record->animal->type . '</span>'
                        . '<br/><span class="text-xs">' . $record->farm->name . '</span>';
                    })
                    ->html()
                    ->url(fn(AnimalProduction $record) => '/animals/' . $record->animal->id),
//                Tables\Columns\TextColumn::make('farm.name'),
//                Tables\Columns\TextColumn::make('animal.type')
//                    ->color('gray'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Litres')
                    ->sortable(),
            ])
            ->defaultSort('date')
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAnimalProductions::route('/'),
        ];
    }
}
