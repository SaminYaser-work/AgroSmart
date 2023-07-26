<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use Filament\Widgets\BarChartWidget;

class WorkerByFarmChart extends BarChartWidget
{
    protected static ?string $heading = 'Total Workers by Farm';

    protected static ?string $pollingInterval = null;

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ]
        ],
    ];

    protected function getData(): array
    {

        $farmWithWorkerCount = \App\Models\Farm::query()
            ->withCount('workers')
            ->get()
            ->toArray();

        return [

            'datasets' => [
                [
                    'data' => array_column($farmWithWorkerCount, 'workers_count'),
                    'backgroundColor' => ['tomato', 'dodgerblue', 'hotpink']
                ],
            ],
            'labels' => array_column($farmWithWorkerCount, 'name'),
        ];
    }
}
