<?php

namespace App\Filament\Resources\FarmingExpensesResource\Widgets;

use App\Models\FarmingExpenses;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class DailyFarmingExpenseLineChart extends ApexChartWidget
{
    protected static ?string $pollingInterval = null;

    protected static string $chartId = 'dailyExpenseLine';

    protected static ?string $heading = 'Daily Expenditure (Last 30 Days)';

    protected int | string | array $columnSpan = 2;

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }

    protected function getOptions(): array
    {
        if (!$this->readyToLoad) {
            return [];
        }

        $data = FarmingExpenses::query()
            ->whereBetween('date', [Carbon::now()->subDays(30), Carbon::now()])
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->toArray();


        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'BasicLineChart',
                    'data' => array_column($data, 'amount')
                ],
            ],
            'xaxis' => [
                'categories' => array_column($data, 'date'),
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
