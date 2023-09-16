<?php

namespace App\Filament\Resources\AnimalExpenseResource\Widgets;

use App\Models\AnimalExpense;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ExpenseType extends ApexChartWidget
{

    protected static string $chartId = 'accountingChart';
    protected static ?string $heading = 'Expense Type Breakdown';
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

        $data = AnimalExpense::query()
            ->groupBy('type')
            ->selectRaw('type, sum(amount) as amount')
            ->get()
            ->toArray();


        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
//            'colors' => ['#C70039', '#2ECC71'],
            'series' => array_column($data, 'amount'),
            'labels' => array_column($data, 'type'),
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
            'plotOptions' => [
                "pie" => [
                    'donut' => [
                        'labels' => [
                            "show" => true,
                            "total" => [
                                "show" => true,
                                "formatter" => "(w) => {
                                const formatter = new Intl.NumberFormat('en-US', {
                                  style: 'currency',
                                  currency: 'BDT',
                                  // These options are needed to round to whole numbers if that's what you want.
                                  //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                                  //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
                                });
                                const total = w.globals.seriesTotals.reduce((a, b) => {
                                  return a + b
                                }, 0)
                                    return formatter.format(total)
                                }"
                            ],
                            "name" => [
                                "show" => true,
                            ],
                            "value" => [
                                "show" => true,
                                "formatter" => "(val) => {
                                const formatter = new Intl.NumberFormat('en-US', {
                                  style: 'currency',
                                  currency: 'BDT',
                                  // These options are needed to round to whole numbers if that's what you want.
                                  //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                                  //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
                                });
                                    return formatter.format(val)
                                }"
                            ],
                        ]
                    ]
                ]
            ],
        ];
    }
}
