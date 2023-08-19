<?php

namespace App\Filament\Resources\FieldResource\Widgets;

use App\Models\Field;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AreaByFarmChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'areaByFarmChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'AreaByFarmChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {


        $data = Field::query()
            ->select('farms.name', \DB::raw('sum(area) as area'))
            ->join('farms', 'farms.id', '=', 'fields.farm_id')
            ->groupBy('farms.name')->get()->toArray();


        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => array_column($data, 'area'),
            'labels' => array_column($data, 'name'),
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
        ];
    }
}
