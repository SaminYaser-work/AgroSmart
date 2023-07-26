<?php

namespace App\Filament\Resources\AttendanceResource\Widgets;

use App\Models\Attendance;
use Filament\Widgets\DoughnutChartWidget;

class PresentTodayChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Present Today';

    protected static ?string $maxHeight = '200px';

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $present = Attendance::query()
            ->where('date', now()->format('Y-m-d'))
            ->whereNotNull('time_in')
            ->count();

        $absent = Attendance::query()
            ->where('date', now()->format('Y-m-d'))
            ->whereNull('time_in')
            ->count();

        return [
            'datasets' => [
                [
                    'data' => [$present, $absent],
                    'backgroundColor' => ['rgb(54, 162, 235)', 'rgb(255, 99, 132)']
                ],
            ],
            'labels' => ['Present', 'Absent'],
        ];
    }
}
