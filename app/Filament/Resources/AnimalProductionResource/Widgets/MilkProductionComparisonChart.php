<?php

namespace App\Filament\Resources\AnimalProductionResource\Widgets;

use App\Models\AnimalProduction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MilkProductionComparisonChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'milkProductionComparisonChart';
    protected static ?string $heading = 'Dairy Production by Month';
    protected static ?int $contentHeight = 300;
    protected static ?string $pollingInterval = null;
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 2;

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }

    protected function getOptions(): array
    {

        if (!$this->readyToLoad) {
            return [];
        }

//        $data = AnimalProduction::query()
//            ->where('type', '=', 'Milk')
//            ->selectRaw('EXTRACT( YEAR_MONTH FROM `date` ) as m, SUM(quantity) as quantity')
//            ->groupByRaw('m')
//            ->get()
//            ->toArray();

        $data = AnimalProduction::query()
            ->where('type', '=', 'Milk')
            ->selectRaw("strftime('%Y-%m', `date`) as m, SUM(quantity) as quantity")
            ->groupByRaw("m")
            ->get()
            ->toArray();

        $data = array_map(function ($item) {
            $item['m'] = Carbon::createFromFormat('Y-m', $item['m'])->format('F Y');
            return $item;
        }, $data);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => '',
                    'data' => array_map(function ($item) {
                        return $item['quantity'];
                    }, $data),
                ],
            ],
            'xaxis' => [
                'categories' => array_column($data, 'm'),
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
                    'text' => 'Liters',
                    'style' => [
                        'color' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
//            'colors' => ['#6366f1'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                    'distributed' => false,
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'gradientToColors' => ['dodgerblue', 'blue'],
                    'shadeIntensity' => 1,
                    'type' => 'vertical',
                    'opacityFrom' => 0.5,
                    'opacityTo' => 1,
                    'stops' => [0, 90, 100]
                ],
            ],
            'legend' => [
                'show' => false,
            ],

            'tooltip' => [
                'y' => [
                    'formatter' => 'function (val) {return "$ " + val + " thousands"}'
                ]
            ]
        ];
    }
}
