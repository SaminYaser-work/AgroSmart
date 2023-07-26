<?php

namespace App\Filament\Resources\AnimalProductionResource\Widgets;

use App\Models\Animal;
use App\Models\AnimalProduction;
use App\Models\Farm;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MilkProductionByFarm extends ApexChartWidget
{

    protected int | string | array $columnSpan = 2;
    protected static string $chartId = 'milkProductionByFarm';
    protected static ?string $heading = 'Dairy Production by Farm';
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

        $data = AnimalProduction::with('farm')
            ->groupBy('farm_id')
            ->selectRaw('farm_id, SUM(quantity) as quantity')
            ->where('type', '=', 'Milk')
            ->get()
            ->toArray();

        $farm_names = array_map(function ($item) {
            return $item['farm']['name'];
        }, $data);
//        dd($farm_names);


        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($data, 'quantity'),
            'labels' => $farm_names,
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
