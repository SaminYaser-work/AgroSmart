<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CropProjectResource\Pages;
use App\Filament\Resources\CropProjectResource\RelationManagers;
use App\Models\CropProject;
use App\Models\Farm;
use App\Models\Field;
use App\Models\Storage;
use App\Utils\Enums;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CropProjectResource extends Resource
{
    protected static ?string $model = CropProject::class;

    protected static ?string $navigationIcon = 'fas-plant-wilt';

    protected static ?string $navigationGroup = 'Crop';
    protected static ?string $navigationLabel = 'Crop Productions';

//    public static function getRecordTitle(?Model $record): string|Htmlable|null
//    {
//        return $record->crop_name . ' Production (' . $record->field->name . ')';
//    }

    protected static ?int $navigationSort = 0;

    protected static ?string $label = 'Crop Productions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('farm_id')
                    ->options(function () {
                        return Farm::all()->pluck('name', 'id');
                    })
                    ->label('Farm')
                    ->afterStateUpdated(function (Closure $set, Closure $get) {
                        $set('field_id',
                            Field::query()
                                ->where('farm_id', $get('farm_id'))
                                ->where('status', '=', false)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->first()
                        );
                    })
                    ->reactive()
                    ->required(),

                Forms\Components\Select::make('crop_name')
                    ->label('Crop')
                    ->options(array_combine(Enums::$CropName, Enums::$CropName))
                    ->required(),

                Forms\Components\Select::make('field_id')
                    ->options(function (Closure $get) {
                        return Field::query()
                            ->where('farm_id', $get('farm_id'))
                            ->pluck('name', 'id');
                    })
                    ->label('Field')
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->default(now()->format('Y-m-d')),

                Forms\Components\DatePicker::make('end_date')
                    ->requiredIf('status', "Stored")
                    ->after('start_date')
                    ->hiddenOn('create'),


                Forms\Components\Select::make('status')
                    ->hiddenOn('create')
                    ->options(array_combine(Enums::$CropStage, Enums::$CropStage))
                    ->reactive()
                    ->required(),

                Forms\Components\TextInput::make('yield')
                    ->requiredIf('status', "Stored")
                    ->label('Yield (tonne)')
                    ->hidden(function (Closure $get) {
                        return $get('status') != "Stored";
                    })
                    ->hiddenOn('create'),

                Forms\Components\Select::make('storage_id')
                    ->hiddenOn('create')
                    ->options(function (Closure $get, CropProject $record) {
                        return Storage::query()
                            ->where('farm_id', $record->farm_id)
                            ->pluck('name', 'id');
                    })
                    ->requiredIf('status', "Stored")
                    ->hidden(function (Closure $get) {
                        return $get('status') != "Stored";
                    })
                    ->label('Storage'),
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
                    ->alignCenter()
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->placeholder('--')
                    ->alignCenter()
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
            'dd' => Pages\DiseaseDetection::route('/disease-detection'),
        ];
    }
}
