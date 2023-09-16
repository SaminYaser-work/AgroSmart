<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use App\Models\Attendance;
use App\Models\SalesOrder;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class IndividualWorkerHourBarChart2 extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'hoursChart';
    protected static ?string $heading = 'Hours Worked (Last 7 Days)';
    protected static ?int $contentHeight = 385;
    protected static ?string $pollingInterval = null;
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 2;
    public Worker|null $record = null;

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }

    protected function getOptions(): array
    {

        if (!$this->readyToLoad) {
            return [];
        }

        $data = Attendance::query()
            ->where('worker_id', $this->record->id)
            ->whereDate('date', '>', now()->subDays(7))
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
            })
            ->toArray();

        $expectedHours = Worker::where('id', $this->record->id)->first()->expected_hours;

//        dd(array_values($data));

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Column',
                    'data' => array_values($data),
                    'type' => 'column',
                ],
                [
                    'name' => 'Line',
                    'data' => array_values($data),
                    'type' => 'line',
                ],
            ],
            'xaxis' => [
                'categories' => array_map( function ($date) {
                    return Carbon::parse($date)->format('d M');
                }, array_keys($data)),

                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],

            ],
            'tooltip' => [
                'enabled' => false,
//                "x" => [
//                    "show" => false,
//                ]
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
                'min' => 0,
                'max' => max(array_values($data)) + 3,
                'title' => [
                    'style' => [
                        'color' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'stroke' => [
                'width' => [0, 3],
                'curve' => 'smooth',
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 5,
                    'horizontal' => false,
                    'distributed' => false,
                ],
            ],
//            'colors' => ['#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#00BCD4', '#009688', '#4CAF50', '#FF9800', '#FF5722'],
            'colors' => ['#6366f1', '#38bdf8'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#d946ef'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'legend' => [
                'show' => false,
            ],
            'annotations' => [
                'yaxis' => [
                    [
                        'y' => $expectedHours,
                        'borderColor' => '#FF5733',
                        'borderWidth' => '3',
                        'label' => [
                            'offsetX' => -6,
                            'offsetY' => -6,
                            'borderColor' => '#f43f5e',
                            'style' => [
                                'color' => '#fffbeb',
                                'background' => '#FF5733',
                            ],
                            'text' => 'Expected Hours',
                        ],
                    ],
                ],
            ],
        ];
    }
}
