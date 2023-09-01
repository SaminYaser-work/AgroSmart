<?php

namespace App\Filament\Widgets;

use App\Models\SalesOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class DeliveredOrderChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'deliveredOrderChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Order Status';

    protected static ?string $pollingInterval = null;
    protected static bool $deferLoading = true;
    protected static ?int $contentHeight = 300;

    protected int|string|array $columnSpan = 2;

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }

    private function getHtmlString(bool $isLoaded) {
        $lastWeekOrders = $this->readyToLoad ? SalesOrder::query()->count() : '&nbsp;';
        $deliveredOrders = $this->readyToLoad ? SalesOrder::query()->whereNotNull('actual_delivery_date')->count(): '&nbsp;';
        $lateOrders = $this->readyToLoad ? SalesOrder::query()
            ->whereNotNull('actual_delivery_date')
            ->whereColumn('actual_delivery_date', '>', 'expected_delivery_date')->count() : '&nbsp;';

        $label1 = $isLoaded ? 'Total' : '&nbsp;';
        $label2 = $isLoaded ? 'Delivered' : '&nbsp;';
        $label3 = $isLoaded ? 'Late' : '&nbsp;';

        return <<< EOD
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <p style="font-size: small; color: #0d47a1;">$label1</p>
                    <p style="font-size: xx-large; color: #0d47a1;">$lastWeekOrders</p>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <p style="font-size: small; color: #00A250;">$label2</p>
                    <p style="font-size: xx-large; color: #00A250;">$deliveredOrders</p>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <p style="font-size: small; color: #c12e2a;">$label3</p>
                    <p style="font-size: xx-large; color: #c12e2a;">$lateOrders</p>
                </div>
            </div>
        EOD;
    }

    protected function getFooter(): string|View
    {
        return new HtmlString($this->getHtmlString($this->readyToLoad));
    }

    protected function getOptions(): array
    {

        if (!$this->readyToLoad) {
            return [];
        }

        $deliveredOrders = SalesOrder::query()->whereNotNull('actual_delivery_date')
            ->count();
        $totalOrders = SalesOrder::query()->count();

        return [
            'chart' => [
                'type' => 'radialBar',
                'height' => 300,
                'offsetY' => -10
            ],
            'series' => [round($deliveredOrders / $totalOrders * 100, 2)],
            'plotOptions' => [
                'radialBar' => [
                    'startAngle' => -135,
                    'endAngle' => 135,
                    'hollow' => [
                        'size' => '70%',
                    ],
                    'dataLabels' => [
                        'show' => true,
                        'name' => [
                            'show' => true,
                            'color' => '#9ca3af',
                            'fontWeight' => 600,
                            'offsetY' => 120
                        ],
                        'value' => [
                            'show' => true,
                            'offsetY' => 76,
                            'color' => '#9ca3af',
                            'fontWeight' => 600,
                            'fontSize' => '20px',
                        ],
                    ],

                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'horizontal',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#ABE5A1', '#00A250'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 50, 100],
                    'colorStops' => []
                ]
            ],
            'stroke' => [
                'dashArray' => 6
            ],
            'labels' => ['Delivered Orders'],
        ];
    }
}
