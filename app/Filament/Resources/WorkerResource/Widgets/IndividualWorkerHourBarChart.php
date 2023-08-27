<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use App\Models\Attendance;
use App\Models\Worker;
use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;

class IndividualWorkerHourBarChart extends BarChartWidget
{
    protected static ?string $heading = 'Attendance of Last 7 Days';
    protected static ?string $pollingInterval = null;
    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
            ]
        ],
        'scales' => [
            'y' => [
                'title' => [
                    'display' => true,
                    'text' => 'Hours'
                ],
                'stacked' => false,
            ],
            'x' => [
                'stacked' => true,
            ]
        ],
    ];
    public Worker|null $record = null;

    protected function getData(): array
    {
        $attendanceReport = Attendance::query()
            ->where('worker_id', $this->record->id)
            ->whereDate('date', '>=', now()->subDays(7))
            ->whereDate('date', '<=', now())
            ->get()
            ->groupBy(function ($attendance) {
                return Carbon::parse($attendance->date)->format('Y-m-d');
            })
            ->map(function ($attendance) {
                return $attendance->sum(function ($attendance) {
                    $timeIn = Carbon::parse($attendance->time_in);
                    $timeOut = Carbon::parse($attendance->time_out);
                    return $timeIn->diffInHours($timeOut);
                });
            });

        $expectedHoursArray = array_fill(0, $attendanceReport->count(), Worker::where('id', $this->record->id)->first()->expected_hours);

        return [

            'datasets' => [
                [
                    'label' => 'Hours Worked',
                    'data' => $attendanceReport->values()->toArray(),
                    'backgroundColor' => ['rgba(0, 191, 255, 0.8)']
                ],
                [
                    'label' => 'Expected Hours',
                    'data' => $expectedHoursArray,
//                    'backgroundColor' => ['rgba(46, 204, 113, 0.8)'],
                    'borderColor' => "rgba(0,0,0,1)",
                    'pointRadius' => 50,
                    'showLine' => false,
                    'pointStyle' => 'line',
                    'type' => "line",
                ]
            ],
            'labels' => $attendanceReport->keys()->toArray(),
        ];
    }
}
