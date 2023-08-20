<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CropProjectResource\Pages;
use App\Filament\Resources\CropProjectResource\RelationManagers;
use App\Models\CropProject;
use App\Utils\Enums;
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
                Forms\Components\Select::make('crop')
                    ->options(array_combine(Enums::$CropName, Enums::$CropName))
                    ->disabledOn('edit'),
                Forms\Components\DatePicker::make('start_date')
                    ->required()->default(now()->format('Y-m-d')),
                Forms\Components\DatePicker::make('end_date')
                    ->requiredIf('status', "Stored")
                    ->after('start_date')->hiddenOn('create'),
                Forms\Components\Select::make('status')
                    ->options(array_combine(Enums::$CropStage, Enums::$CropStage))
                    ->required(),
                Forms\Components\TextInput::make('yield')
                    ->requiredIf('status', "Stored")
                    ->label('Yield (tonne)')
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('crop_name')->label('Crop'),
                Tables\Columns\TextColumn::make('field_name')
                    ->getStateUsing(function (CropProject $record) {
                        return $record->field->name . '<br/>' . '<span class="text-xs">' . ucwords($record->farm->name) . '</span>';
                    })
                    ->html()
                    ->label('Field'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date(),
                Tables\Columns\TextColumn::make('expected_end_date')
                    ->date(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('yield')->label('Yield (tonne)'),
                Tables\Columns\TextColumn::make('expected_yield')->label('Expected Yield (tonne)'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->disabled(function (CropProject $record) {
                    return $record->status == "Stored";
                }),
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
