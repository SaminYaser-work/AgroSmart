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

    protected static ?string $navigationIcon = 'heroicon-o-collection';

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
                        return $record->name . '<span class="' . Enums::$badgeClasses . '">' . $record->water_type . '</span>'
                            . '<br/>' . '<span class="text-xs">' . ucwords($record->farm->name) . '</span>';
                    })
                    ->html()
                    ->label('Pond Name'),
//                Tables\Columns\TextColumn::make('name'),
//                Tables\Columns\TextColumn::make('pond_type'),
//                Tables\Columns\TextColumn::make('water_type'),
                Tables\Columns\TextColumn::make('fish')->label('Current Fish'),
                Tables\Columns\TextColumn::make('size')->label("Pond Size"),
            ])
            ->filters([
                //
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
