<?php

namespace App\Filament\Widgets;

use App\Models\Animal;
use App\Models\PurchaseOrder;
use App\Models\Salary;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ExpenseBreakdownChart extends ApexChartWidget
{

    protected int | string | array $columnSpan = 2;
    protected static string $chartId = 'expenseBreakdownChart';
    protected static ?string $heading = 'Expense Breakdown';
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

        // Expenses
        // - Salary
        // - Purchase orders
        // - Daily expenses (not implemented yet)
            // - Animal expenses
            // - Storage expenses
            // - Fish expenses
            // - Crop expenses

        $data = [
            [
                'expense' => 'Salaries',
                'amount' => Salary::sum('total')
            ],
            [
                'expense' => 'Purchases',
                'amount' => PurchaseOrder::sum('amount'),
            ],
        ];



        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($data, 'amount'),
            'labels' => array_column($data, 'expense'),
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
            'tooltip' => [
                'custom' => "({series, seriesIndex, dataPointIndex, w}) => {
                    const formatter = new Intl.NumberFormat('en-US', {
                      style: 'currency',
                      currency: 'BDT',
                      // These options are needed to round to whole numbers if that's what you want.
                      //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                      //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
                    });
                    return w.globals.labels[seriesIndex] + ': ' + formatter.format(series[seriesIndex])
                }",
            ]
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
