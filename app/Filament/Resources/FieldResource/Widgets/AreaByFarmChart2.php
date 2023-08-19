<?php

namespace App\Filament\Resources\FieldResource\Widgets;

use App\Models\Attendance;
use App\Models\Field;
use Filament\Widgets\DoughnutChartWidget;

class AreaByFarmChart2 extends DoughnutChartWidget
{
    protected static ?string $heading = 'Total Area by Farm';

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {

        $data = Field::query()
            ->select('farms.name', \DB::raw('sum(area) as area'))
            ->join('farms', 'farms.id', '=', 'fields.farm_id')
            ->groupBy('farms.name')->get()->toArray();

        return [
            'datasets' => [
                [
                    'data' => array_column($data, 'area'),
                    'backgroundColor' => ['rgb(54, 162, 235)', 'rgb(255, 99, 132)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)', 'rgb(201, 203, 207)']
                ],
            ],
            'labels' => array_column($data, 'name'),
        ];
    }
}
