<?php

namespace App\Filament\Resources\SalaryResource\Widgets;

use App\Models\AnimalProduction;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SalaryCompByMonth extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'milkProductionComparisonChart';
    protected static ?string $heading = 'Salary Paid per Month';
    protected static ?int $contentHeight = 300;
    protected static ?string $pollingInterval = null;
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 1;

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }

    protected function getOptions(): array
    {

        if (!$this->readyToLoad) {
            return [];
        }

        $data = Salary::query()
            ->select([
                'month',
                \DB::raw('SUM(total) as total')
            ])
            ->groupBy('month')
            ->where('paid', '=', true)
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
                    'data' => array_map(function ($item) {
                        return round($item['total'], 2);
                    }, $data),
                ],
            ],
            'xaxis' => [
                'categories' => array_map(function ($item) {
                    return date('F', mktime(0, 0, 0, $item['month'], 10));
                }, $data),
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
