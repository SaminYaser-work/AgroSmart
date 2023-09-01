<?php

namespace App\Filament\Resources\PondResource\Pages;

use App\Filament\Resources\PondResource;
use App\Models\Farm;
use App\Models\Pond;
use App\Utils\Enums;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPonds extends ListRecords
{
    protected static string $resource = PondResource::class;

    protected function getHeaderWidgetsColumns(): int|string|array
    {
        return 4;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PondResource\Widgets\PondStats::class,
            PondResource\Widgets\PondsPerFishTypeChart::class,
            PondResource\Widgets\WaterTypeChart::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('export')
                ->action(function (array $data) {
                    Pond::query()
                        ->where('id', '=', $data['pond'])
                        ->update([
                            'fish' => $data['fish_type'],
                        ]);

                    Notification::make()
                        ->title('New Fish Production Added')
                        ->send();
                })
                ->label('Start New Fish Production')
                ->form([
                    Select::make('farm')
                        ->options(
                            Farm::all()->pluck('name', 'id')
                        )
                        ->reactive()
//                        ->afterStateUpdated(fn(Closure $set) => $set('pond', null))
                        ->required(),
                    Select::make('pond')
                        ->options(function (Closure $get) {
                            return Pond::query()
                                ->where('farm_id', $get('farm'))
                                ->whereNull('fish')
                                ->pluck('name', 'id');
                        })
                        ->reactive()
                        ->hidden(fn(Closure $get) => $get('farm') === null)
                        ->required(),
                    Select::make('fish_type')
                        ->options(array_combine(Enums::$FishName, Enums::$FishName))
                        ->reactive()
                        ->required()
                        ->hidden(fn(Closure $get) => $get('pond') === null)
                        ->placeholder('Select Fish Type'),
                    TextInput::make('initial_fish_count')
                        ->numeric()
                        ->label('Fish Count')
                        ->hidden(fn(Closure $get) => $get('fish_type') === null)
                        ->required(),
                    TextInput::make('biomass')
                        ->numeric()
                        ->label('Initial Biomass (Kg)')
                        ->hidden(fn(Closure $get) => $get('fish_type') === null)
                        ->required()
                ]),
            Actions\CreateAction::make()->label('Add New Pond'),
        ];
    }
}
