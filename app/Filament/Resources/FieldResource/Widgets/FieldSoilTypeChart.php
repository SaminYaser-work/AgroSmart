<?php

namespace App\Filament\Resources\FieldResource\Widgets;

use App\Models\Attendance;
use App\Models\Field;
use Filament\Widgets\DoughnutChartWidget;

class FieldSoilTypeChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Soil Types';

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {

        $data = Field::query()
            ->groupBy('soil_type')
            ->select('soil_type', \DB::raw('count(*) as count'))
            ->get()->toArray();


        return [
            'datasets' => [
                [
                    'data' => array_column($data, 'count'),
                    'backgroundColor' => ['rgb(54, 162, 235)', 'rgb(255, 99, 132)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)', 'rgb(201, 203, 207)']
                ],
            ],
            'labels' => array_column($data, 'soil_type'),
        ];
    }
}
