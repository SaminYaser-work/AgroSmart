<?php

namespace App\Filament\Resources\StorageResource\Widgets;

use App\Models\Storage;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class FreeSpaceChart extends ApexChartWidget
{

    protected int | string | array $columnSpan = 1;
    protected static string $chartId = 'storageSpace';
    protected static ?string $heading = 'Storage Capacity';
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

        $max_capacity = Storage::sum('capacity');
        $current_capacity = Storage::sum('current_capacity');
        $free_capacity = $max_capacity - $current_capacity;

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => [$max_capacity, $free_capacity],
            'labels' => ['Occupied', 'Free'],
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
