<?php

namespace App\Filament\Resources\PondResource\Widgets;

use App\Models\Pond;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PondsPerFishTypeChart extends ApexChartWidget
{

    protected int | string | array $columnSpan = 1;
    protected static string $chartId = 'pondsPerFishType';
    protected static ?string $heading = 'Ponds Per Fish Type';
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
            ->groupBy('fish')
            ->selectRaw('fish, COUNT(name) as quantity')
            ->whereNotNull('fish')
            ->get()
            ->toArray();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($data, 'quantity'),
            'labels' => array_column($data, 'fish'),
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
