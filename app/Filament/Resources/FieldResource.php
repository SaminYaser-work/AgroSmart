<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FieldResource\Pages;
use App\Filament\Resources\FieldResource\RelationManagers;
use App\Models\Field;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static ?string $navigationIcon = 'fas-grip';
    protected static ?string $navigationGroup = 'Crop';

    private static string $badgeClasses = 'min-h-6 inline-flex items-center justify-center space-x-1 whitespace-nowrap rounded-xl px-2 py-0.5 text-sm font-medium tracking-tight rtl:space-x-reverse text-gray-700 bg-gray-500/10';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('area')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('soil_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(function (Field $record) {
                        return $record->name . '&nbsp;<span class="' .
                            FieldResource::$badgeClasses . '">' . $record->soil_type . '</span><br>' .
                            '<span class="text-xs text-gray-700">' . $record->farm->name . '</span>';
                    })
                    ->html()
                    ->searchable(['name', 'soil_type', 'farm.name']),
                Tables\Columns\IconColumn::make('status')
                    ->label('Availability')
                    ->trueIcon('fas-check-circle')
                    ->falseIcon('fas-times-circle'),
                Tables\Columns\TextColumn::make('area')->sortable(),
                Tables\Columns\TextColumn::make('address')->words(5)->tooltip(function (Field $record) {
                    return $record->address;
                }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        true => 'Available',
                        false => 'Unavailable',
                    ])
                    ->label('Availability'),
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
            'index' => Pages\ManageFields::route('/'),
        ];
    }
}
