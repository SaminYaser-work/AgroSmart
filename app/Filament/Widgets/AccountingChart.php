<?php

namespace App\Filament\Widgets;

use App\Models\PurchaseOrder;
use App\Models\Salary;
use App\Models\SalesOrder;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AccountingChart extends ApexChartWidget
{

    protected static string $chartId = 'accountingChart';
    protected static ?string $heading = 'Accounts Payable vs Receivable';
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

        $data = [
            [
                'category' => 'Accounts Payable',
                'amount' => $this->getPayable()
            ],
            [
                'category' => 'Accounts Receivable',
                'amount' => $this->getReceivable()
            ],
        ];


        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'colors' => ['#C70039', '#2ECC71'],
            'series' => array_column($data, 'amount'),
            'labels' => array_column($data, 'category'),
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

    private function getPayable(): float
    {
        $salaries = Salary::query()->where('paid', '=', false)->sum('total');
        $purchases = PurchaseOrder::query()->where('paid', '=', false)->sum('amount');

        return $salaries + $purchases;
    }

    private function getReceivable(): float
    {
        return SalesOrder::query()->whereNull('actual_delivery_date')->sum('amount');
    }
}
