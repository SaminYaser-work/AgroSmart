<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorageResource\Pages;
use App\Filament\Resources\StorageResource\RelationManagers;
use App\Models\Storage;
use App\Utils\Enums;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use RyanChandler\FilamentProgressColumn\ProgressColumn;

class StorageResource extends Resource
{
    protected static ?string $model = Storage::class;

    protected static ?string $navigationIcon = 'fas-warehouse';
    protected static ?string $navigationGroup = 'Inventory';


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
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('capacity')
                    ->required(),
                Forms\Components\TextInput::make('current_capacity')
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
                    ->getStateUsing(function (Storage $record) {
                        return $record->name . '&nbsp;<span class="' . Enums::$badgeClasses . '">' . $record->type . '</span>'
                            . '<br/>' . '<span class="text-xs">' . $record->farm->name . '</span>';
                    })->html(),
                Tables\Columns\TextColumn::make('capacity')
                    ->getStateUsing(function (Storage $record) {
                        return $record->capacity . ' ' . $record->unit;
                    }),
                Tables\Columns\TextColumn::make('current_capacity')
                    ->getStateUsing(function (Storage $record) {
                        return $record->current_capacity . ' ' . $record->unit;
                    }),
                ProgressColumn::make('free_capacity')
                    ->label('Free Space')
                    ->progress(fn(Storage $record) => round(100 - ($record->current_capacity / $record->capacity * 100), 2))
                    ->color(fn(Storage $record) => 100 - ($record->current_capacity / $record->capacity * 100) > 50 ? 'success' : 'danger')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Update'),
//                Tables\Actions\DeleteAction::make()->label('Remove'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStorages::route('/'),
        ];
    }
}
