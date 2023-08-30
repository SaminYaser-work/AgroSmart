<?php

namespace App\Filament\Widgets;

use App\Models\AnimalProduction;
use App\Models\Farm;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TempLineChart extends ApexChartWidget
{
    protected static string $chartId = 'tempLineChart';
    protected static ?string $pollingInterval = null;
    protected static bool $deferLoading = true;

    protected function getHeading(): ?string
    {
        return 'Hourly Temperature Report ' . Carbon::now()->format('d M, Y') . ' to ' . Carbon::now()->addDays(1)->format('d M, Y');
    }

    protected int | string | array $columnSpan = 4;

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }

    protected function getOptions(): array
    {

        if (!$this->readyToLoad) {
            return [];
        }

        $response = Http::get('https://api.open-meteo.com/v1/forecast?latitude=23.7104&longitude=90.4074&hourly=temperature_2m,relativehumidity_2m,rain');
        $data = $response->json();


        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'tempLineChart',
                    'data' => array_slice($data['hourly']['temperature_2m'], 0, 48),
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'xaxis' => [
                'categories' => array_slice(array_map(function ($hour) {
                    return Carbon::parse($hour)->format('h A, D');
                }, $data['hourly']['time']), 0 , 48),
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
                'title' => [
                    'text' => 'Temperature (°C)'
                ]
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
//                    'shade' => 'dark',
//                    'gradientToColors' => ['red'],
                    'shadeIntensity' => 1,
//                    'type' => 'horizontal',
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.9,
                    'stops' => [0, 90, 100]
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'markers' => [
                'size' => 0,
            ],
        ];
    }
}
