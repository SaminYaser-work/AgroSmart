<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CustomerProfileTopProducts extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'customerProfileTopProducts';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Top Products';

    protected int | string | array $columnSpan = 1;

    private $gradientColors = [
        ['#00ADEF', '#007AD0'],
        ['#F44336', '#D32F2F'],
        ['#FFC107', '#FF9800'],
        ['#4CAF50', '#388E3C'],
        ['#9C27B0', '#7B1FA2'],
    ];

    public Customer|null $record = null;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {

        $topTypes = PurchaseOrder::select('name', \DB::raw('count(name) as count'))
            ->where('customer_id', $this->record->id)
            ->groupBy('name')
            ->orderByDesc('count')
            ->get()
            ->toArray();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => array_column($topTypes, 'count'),
            'labels' => array_column($topTypes, 'name'),
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100]
                ],
            ],
        ];
    }
}
