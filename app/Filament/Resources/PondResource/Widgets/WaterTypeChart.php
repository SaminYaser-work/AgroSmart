<?php

namespace App\Filament\Resources\PondResource\Widgets;

use App\Models\Pond;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class WaterTypeChart extends ApexChartWidget
{

    protected int | string | array $columnSpan = 2;
    protected static string $chartId = 'waterType';
    protected static ?string $heading = 'Water Types';
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

        $data = Pond::query()
            ->groupBy('water_type')
            ->selectRaw('water_type, COUNT(*) as quantity')
            ->get()
            ->toArray();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($data, 'quantity'),
            'labels' => array_column($data, 'water_type'),
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
