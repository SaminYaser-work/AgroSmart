<?php

namespace App\Filament\Resources\SupplierResource\Widgets;

use App\Models\Attendance;
use App\Models\Field;
use App\Models\Supplier;
use Filament\Widgets\DoughnutChartWidget;

class SupplierTypeChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Supplier Types';
    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {

        $data = Supplier::query()
            ->groupBy('type')
            ->selectRaw('count(*) as count, type')
            ->get()
            ->toArray();


        return [
            'datasets' => [
                [
                    'data' => array_column($data, 'count'),
                    'backgroundColor' => ['rgb(54, 162, 235)', 'rgb(255, 99, 132)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)', 'rgb(201, 203, 207), rgb(255, 159, 64), rgb(255, 99, 132), rgb(75, 192, 192), rgb(153, 102, 255), rgb(201, 203, 207)']
                ],
            ],
            'labels' => array_column($data, 'type'),
        ];
    }
}
