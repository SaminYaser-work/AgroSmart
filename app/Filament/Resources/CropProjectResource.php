<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CropProjectResource\Pages;
use App\Filament\Resources\CropProjectResource\RelationManagers;
use App\Models\CropProject;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CropProjectResource extends Resource
{
    protected static ?string $model = CropProject::class;

    protected static ?string $navigationIcon = 'fas-plant-wilt';

    protected static ?string $navigationGroup = 'Crop';
    protected static ?string $navigationLabel = 'Crop Productions';

    protected static ?string $label = 'Crop Productions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('field_id')
                    ->required(),
                Forms\Components\TextInput::make('farm_id')
                    ->required(),
                Forms\Components\TextInput::make('crop_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('yield')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('field_id'),
                Tables\Columns\TextColumn::make('farm_id'),
                Tables\Columns\TextColumn::make('crop_name'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('yield'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
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
            'index' => Pages\ListCropProjects::route('/'),
            'create' => Pages\CreateCropProject::route('/create'),
            'edit' => Pages\EditCropProject::route('/{record}/edit'),
        ];
    }
}
