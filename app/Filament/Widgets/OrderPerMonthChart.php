<?php

namespace App\Filament\Widgets;

use App\Models\AnimalProduction;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class OrderPerMonthChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'orderPerMonthChart';
    protected static ?string $heading = 'Orders per Month';
    protected static ?int $contentHeight = 385;
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

        $data = SalesOrder::query()
            ->selectRaw("strftime('%m', order_date) as month, count(strftime('%m', order_date)) as total_orders")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => '',
                    'data' => array_column($data, 'total_orders'),
                ],
            ],
            'xaxis' => [
                'categories' => array_map( function ($month) {
                    return Carbon::create()->month($month)->format('F');
                }, array_column($data, 'month')),
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
                    'style' => [
                        'color' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                    'distributed' => true,
                ],
            ],
//            'colors' => ['#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#00BCD4', '#009688', '#4CAF50', '#FF9800', '#FF5722'],
            'fill' => [
                'colors' => ['#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#00BCD4', '#009688', '#4CAF50', '#FF9800', '#FF5722'],
//                'type' => 'gradient',
//                'gradient' => [
//                    'shade' => 'dark',
//                    'gradientToColors' => ['dodgerblue', 'blue'],
//                    'shadeIntensity' => 1,
//                    'type' => 'vertical',
//                    'opacityFrom' => 0.5,
//                    'opacityTo' => 1,
//                    'stops' => [0, 90, 100]
//                ],
            ],
            'legend' => [
                'show' => false,
            ],
        ];
    }
}
