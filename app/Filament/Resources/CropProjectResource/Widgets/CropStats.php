<?php

namespace App\Filament\Resources\CropProjectResource\Widgets;

use App\Models\CropProject;
use App\Models\Field;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Http;

class CropStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected function getCards(): array
    {
        $totalRunningProductions = CropProject::count();

        $response = Http::get('https://api.open-meteo.com/v1/forecast?latitude=23.7104&longitude=90.4074&hourly=temperature_2m,relativehumidity_2m,rain');
        $data = $response->json();
        $avgTemp = round(array_sum($data['hourly']['temperature_2m']) / count($data['hourly']['temperature_2m']), 1);
        $avgHumidity = round(array_sum($data['hourly']['relativehumidity_2m']) / count($data['hourly']['relativehumidity_2m']), 2);

        return [
            Card::make('Temperature', $avgTemp . ' °C')
                ->chart($data['hourly']['temperature_2m'])
                ->description($avgHumidity . '% Humidity')
                ->color('success')
                ->descriptionIcon('fas-sun-plant-wilt'),
            Card::make('Total Running Productions', $totalRunningProductions)
                ->description('Out of ' . Field::count() . ' Fields')
                ->descriptionIcon('fas-layer-group'),
        ];
    }
}
