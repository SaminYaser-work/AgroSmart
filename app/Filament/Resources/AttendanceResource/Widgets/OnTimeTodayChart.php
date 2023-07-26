<?php

namespace App\Filament\Resources\AttendanceResource\Widgets;

use App\Models\Attendance;
use Filament\Widgets\DoughnutChartWidget;

class OnTimeTodayChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'On Time Today';

    protected static ?string $maxHeight = '200px';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $onTime = Attendance::query()
            ->where('date', now()->format('Y-m-d'))
            ->where('time_in', '<=', '08:00:00')
            ->count();

        $late = Attendance::query()
            ->where('date', now()->format('Y-m-d'))
            ->where('time_in', '>', '08:00:00')
            ->count();

        return [
            'datasets' => [
                [
                    'data' => [$onTime, $late],
                    'backgroundColor' => ['rgb(54, 162, 235)', 'rgb(255, 99, 132)']
                ],
            ],
            'labels' => ['On Time', 'Late'],
        ];
    }
}
