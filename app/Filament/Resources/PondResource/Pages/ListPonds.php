<?php

namespace App\Filament\Resources\PondResource\Pages;

use App\Filament\Resources\PondResource;
use App\Models\Farm;
use App\Models\Pond;
use App\Utils\Enums;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPonds extends ListRecords
{
    protected static string $resource = PondResource::class;

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
                        ->required(),
                    Select::make('pond')
                        ->options(function (Closure $get) {
                            $ponds = Pond::where('farm_id', $get('farm'))->get();
                            return $ponds->pluck('name', 'id');
                        })
                        ->hidden(fn(Closure $get) => $get('farm') === null)
                        ->required(),
                    Select::make('Fish Type')
                        ->options(array_combine(Enums::$FishName, Enums::$FishName))
                        ->required()
                        ->hidden(fn(Closure $get) => $get('pond') === null && $get('farm') === null)
                        ->placeholder('Select Fish Type'),
                ]),
            Actions\CreateAction::make()->label('Add New Pond'),
        ];
    }
}
