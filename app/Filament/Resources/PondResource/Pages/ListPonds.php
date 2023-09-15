<?php

namespace App\Filament\Resources\PondResource\Pages;

use App\Filament\Resources\PondResource;
use App\Models\Farm;
use App\Models\Pond;
use App\Models\PondMetrics;
use App\Utils\Enums;
use Closure;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;
use Livewire\Features\Placeholder;

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
            Actions\Action::make('start_new_fish_production')
                ->action(function (array $data) {
                    Pond::query()
                        ->where('id', '=', $data['pond'])
                        ->update([
                            'fish' => $data['fish_type'],
                            'initial_biomass' => $data['biomass'],
                            'initial_fish_count' => $data['initial_fish_count'],
                        ]);

                    Notification::make()
                        ->title('New Fish Production Added')
                        ->send();
                })
                ->label('Start New Fish Production')
                ->form(
                    [
                        Select::make('farm')
                            ->options(
                                Farm::all()->pluck('name', 'id')
                            )
                            ->reactive()
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
                            ->afterStateUpdated(function (Closure $set, $state) {
                                if ($state === null) {
                                    return;
                                }
                                $values = array_values(array_slice(PondMetrics::query()
                                    ->where('pond_id', $state)
                                    ->first()
                                    ->toArray(), 1, 3));
                                $set('pred', $this->getFishPrediction($values));
                            })
                            ->required(),
                        TextInput::make('pred')
                            ->default('Select a Pond to see suggestion')
                            ->label(new HtmlString('Suggested Fish <span style="font-weight: bolder; background: linear-gradient(to right, blue, violet); -webkit-background-clip: text; color: transparent; display: inline-block; animation: gradientAnimation 3s linear infinite;">(AI)</span>'))
                            ->hidden(fn(Closure $get) => $get('pond') === null)
                            ->disabled()
                            ->required(false),
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
                    ]
                ),
            Actions\CreateAction::make()->label('Add New Pond'),
        ];
    }

    private function getFishPrediction(array $values): string
    {
        try {
            $response = \Http::post('https://agrosmartai.azurewebsites.net/fish', [
                "metric" => $values
            ]);
            $res = $response->json();
            if (array_key_exists('fish', $res)) {
                return $res['fish'] . ' (' . ($res['confidence'] * 100) . '%)';
            }
            \Log::error('Error in AI response: ' . $res);
            return 'Error';
        } catch (\Exception $e) {
            \Log::error('Error in AI response: ' . $e->getMessage());
            return 'Error';
        }
    }
}
