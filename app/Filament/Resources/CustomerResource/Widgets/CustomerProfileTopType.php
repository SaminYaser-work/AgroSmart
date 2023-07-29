<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CustomerProfileTopType extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'customerProfileTopType';

    protected static bool $deferLoading = true;
    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Ordered Product Types';

    protected int | string | array $columnSpan = 1;

    public Customer|null $record = null;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        if (!$this->readyToLoad) {
            return [];
        }

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
