<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerResource\Pages;
use App\Filament\Resources\WorkerResource\RelationManagers;
use App\Filament\Resources\WorkerResource\Widgets\WorkerByFarmChart;
use App\Models\Worker;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'HCM';

    protected static ?string $recordTitleAttribute = 'last_name';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('farm_id')
                            ->relationship('farm', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date'),
                        Forms\Components\TextInput::make('salary')
                            ->required(),
                        Forms\Components\TextInput::make('bonus')
                            ->required(),
                        Forms\Components\TextInput::make('over_time_rate')
                            ->required(),
                        Forms\Components\TextInput::make('expected_hours')
                            ->required(),
                        Forms\Components\TextInput::make('designation')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(function (Worker $record) {
                        return $record->first_name . ' ' . $record->last_name
                            . '<br/>' . '<span class="text-xs">' . ucwords($record->designation) . '</span>'
                        . '<br/>' . '<span class="text-xs">' . $record->farm->name . '</span>';
                    })
                    ->html()
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('phone_number')->icon('heroicon-o-phone')->searchable(),
                Tables\Columns\TextColumn::make('salary')->sortable()->money('bdt', true),
                Tables\Columns\TextColumn::make('bonus')->sortable()->money('bdt', true),
                Tables\Columns\TextColumn::make('over_time_rate')->sortable()->money('bdt', true),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListWorkers::route('/'),
            'create' => Pages\CreateWorker::route('/create'),
            'edit' => Pages\EditWorker::route('/{record}'),
        ];
    }
}
