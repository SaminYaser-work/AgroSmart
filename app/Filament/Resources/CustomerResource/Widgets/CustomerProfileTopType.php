<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CustomerProfileTopType extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'customerProfileTopType';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Top Product Types';

    protected int | string | array $columnSpan = 2;

    public Customer|null $record = null;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {

        $topTypes = PurchaseOrder::select('type', \DB::raw('count(type) as count'))
            ->where('customer_id', $this->record->id)
            ->groupBy('type')
            ->orderByDesc('count')
            ->get()
            ->toArray();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => array_column($topTypes, 'count'),
            'labels' => array_column($topTypes, 'type'),
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
        ];
    }
}
