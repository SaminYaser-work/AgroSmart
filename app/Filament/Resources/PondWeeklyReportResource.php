<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PondWeeklyReportResource\Pages;
use App\Filament\Resources\PondWeeklyReportResource\RelationManagers;
use App\Models\Pond;
use App\Models\PondWeeklyReport;
use App\Utils\Enums;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class PondWeeklyReportResource extends Resource
{
    protected static ?string $model = PondWeeklyReport::class;

    protected static ?string $navigationIcon = 'fas-folder-open';
    protected static ?string $navigationGroup = 'Fishery';
    protected static ?string $navigationLabel = 'Weekly Reports';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pond_id')
                    ->relationship('pond', 'name')
                    ->required(),
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('production')
                    ->required(),
                Forms\Components\TextInput::make('yield')
                    ->required(),
                Forms\Components\TextInput::make('survival_rate')
                    ->required(),
                Forms\Components\TextInput::make('average_weight')
                    ->required(),
                Forms\Components\TextInput::make('average_growth')
                    ->required(),
                Forms\Components\TextInput::make('dissolved_oxygen')
                    ->required(),
                Forms\Components\TextInput::make('water_level')
                    ->required(),
                Forms\Components\TextInput::make('water_temperature')
                    ->required(),
                Forms\Components\TextInput::make('ph')
                    ->required(),
                Forms\Components\TextInput::make('turbidity')
                    ->required(),
                Forms\Components\TextInput::make('ammonia')
                    ->required(),
                Forms\Components\TextInput::make('nitrate')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date(),
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(function (PondWeeklyReport $record) {
                        return $record->pond->name
                            . '<br/>' . '<span class="text-xs">' . $record->pond->fish . '</span>'
                            . '<br/>' . '<span class="text-xs">' . $record->farm->name . '</span>';
                    })->html()->label('Pond'),
                Tables\Columns\TextColumn::make('production')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->production, 2) . ' kg';
                }),
                Tables\Columns\TextColumn::make('yield')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->yield, 2) . ' kg';
                }),
                Tables\Columns\TextColumn::make('survival_rate')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->survival_rate, 2) . '%';
                }),
                Tables\Columns\TextColumn::make('average_weight')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->average_weight, 2) . ' kg';
                }),
                Tables\Columns\TextColumn::make('average_growth')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->average_growth, 2) . ' cm';
                }),
                Tables\Columns\TextColumn::make('dissolved_oxygen')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->dissolved_oxygen, 2) . ' mg/L';
                }),
                Tables\Columns\TextColumn::make('water_level')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->water_level, 2) . ' m';
                }),
                Tables\Columns\TextColumn::make('water_temperature')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->water_temperature, 2) . ' °C';
                }),
                Tables\Columns\TextColumn::make('ph')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->ph, 2) . ' pH';
                }),
                Tables\Columns\TextColumn::make('turbidity')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->turbidity, 2) . ' NTU';
                }),
                Tables\Columns\TextColumn::make('ammonia')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->ammonia, 2) . ' mg/L';
                }),
                Tables\Columns\TextColumn::make('nitrate')->getStateUsing(function (PondWeeklyReport $record) {
                    return round($record->nitrate, 2) . ' mg/L';
                })
            ])
            ->defaultSort('date', 'desc')
            ->filters([

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
            'index' => Pages\ListPondWeeklyReports::route('/'),
            'create' => Pages\CreatePondWeeklyReport::route('/create'),
            'edit' => Pages\EditPondWeeklyReport::route('/{record}/edit'),
        ];
    }
}
