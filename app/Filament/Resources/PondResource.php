<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PondResource\Pages;
use App\Filament\Resources\PondResource\RelationManagers;
use App\Models\Pond;
use App\Utils\Enums;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class PondResource extends Resource
{
    protected static ?string $model = Pond::class;

    protected static ?string $navigationIcon = 'fas-water';
    protected static ?string $navigationGroup = 'Fishery';

    protected static ?string $navigationLabel = 'Production';

    protected static ?string $label = 'Fish Productions';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pond_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('water_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('fish')
                    ->maxLength(255),
                Forms\Components\TextInput::make('size')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(function (Pond $record) {
                        return $record->name . '&nbsp;<span class="' . Enums::$badgeClasses . '">' . $record->water_type . '</span>'
                            . '<br/>' . '<span class="text-xs">' . ucwords($record->farm->name) . '</span>';
                    })
                    ->html()
                    ->label('Pond Name'),
                Tables\Columns\TextColumn::make('fish')->label('Current Fish')->placeholder('--')->alignCenter(),
                Tables\Columns\TextColumn::make('size')->label("Pond Size")
                    ->getStateUsing(function (Pond $record) {
                        return $record->size . ' m<sup>2</sup>';
                    })
                    ->html()
                    ->label('Size'),
            ])
            ->defaultSort('fish', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('water_type')
                    ->options(array_combine(Enums::$WaterType, Enums::$WaterType))
                    ->label('Water Type'),
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
            'index' => Pages\ListPonds::route('/'),
            'create' => Pages\CreatePond::route('/create'),
            'edit' => Pages\EditPond::route('/{record}/edit'),
        ];
    }
}
