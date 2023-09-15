<?php

namespace App\Filament\Resources\AnimalResource\Widgets;

use App\Models\Animal;
use App\Models\AnimalProduction;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CowBreedPieChart extends ApexChartWidget
{

    protected int | string | array $columnSpan = 2;
    protected static string $chartId = 'cowBreedPieChart';
    protected static ?string $heading = 'Types of Cow by Breed';
    protected static ?int $contentHeight = 300;
    protected static ?string $pollingInterval = null;
    protected static bool $deferLoading = true;

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }


    protected function getOptions(): array
    {

        if(!$this->readyToLoad) {
            return [];
        }

        $data = Animal::query()
            ->groupBy('breed')
            ->selectRaw('breed, COUNT(*) as quantity')
            ->get()
            ->toArray();


        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($data, 'quantity'),
            'labels' => array_column($data, 'breed'),
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
//            'fill' => [
//                'type' => 'gradient',
//                'gradient' => [
//                    'shade' => 'dark',
//                    'gradientToColors' => ['dodgerblue', 'blue'],
//                    'shadeIntensity' => 1,
//                    'type' => 'vertical',
//                    'opacityFrom' => 1,
//                    'opacityTo' => 1,
//                    'stops' => [0, 90, 100]
//                ],
//            ],
        ];
    }
}
