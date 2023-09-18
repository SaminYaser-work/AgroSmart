<?php

namespace App\Filament\Resources\FarmingExpensesResource\Widgets;

use App\Models\FarmingExpenses;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MonthlyFarmingExpenseBarChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'expensePerMonth';
    protected static ?string $heading = 'Expense per Month';
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

        $data = FarmingExpenses::query()
            ->selectRaw('sum(amount) as total_expense, strftime("%m", date) as month')
            ->groupBy('month')
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
                    'data' => array_column($data, 'total_expense'),
                ],
            ],
            'dataLabels' => [
                "enabled" => true,
                "formatter" => "(value) => {return value.toLocaleString()}"
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
                    "formatter" => "(value) => {
                                const formatter = new Intl.NumberFormat('en-US', {
                                  style: 'currency',
                                  currency: 'BDT',
                                  // These options are needed to round to whole numbers if that's what you want.
                                  //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                                  //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
                                });
                                   return formatter.format(value)
                                }"
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
