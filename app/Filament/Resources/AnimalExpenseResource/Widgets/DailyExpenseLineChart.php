<?php

namespace App\Filament\Resources\AnimalExpenseResource\Widgets;

use App\Models\AnimalExpense;
use Carbon\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class DailyExpenseLineChart extends ApexChartWidget
{
//    protected static ?string $pollingInterval = null;

    protected static string $chartId = 'dailyExpenseLine';

    protected static ?string $heading = 'dailyExpenseLine';

    protected function getOptions(): array
    {

        $data = AnimalExpense::query()->limit(20)->pluck('amount')->toArray();
//        $days = Carbon::now()->daysUntil(Carbon::now()->subDays(20))->toArray();


        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'BasicLineChart',
                    'data' => $data
                ],
            ],
            'xaxis' => [
                'categories' => Carbon::now()->daysUntil(Carbon::now()->subDays(20))->toArray(),
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'colors' => ['#6366f1'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }
}
