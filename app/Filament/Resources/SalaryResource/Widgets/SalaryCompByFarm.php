<?php

namespace App\Filament\Resources\SalaryResource\Widgets;

use App\Models\AnimalProduction;
use App\Models\Salary;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SalaryCompByFarm extends ApexChartWidget
{
    protected int | string | array $columnSpan = 1;
    protected static string $chartId = 'salaryFarm';
    protected static ?string $heading = 'Salary Paid by Farm';
    protected static ?int $contentHeight = 300;
    protected static ?string $pollingInterval = null;
    protected static bool $deferLoading = true;

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }


    protected function getOptions(): array
    {

        if(!$this->readyToLoad) {
            return [];
        }

        $data = Salary::with('farm')
            ->groupBy('farm_id')
            ->selectRaw('farm_id, SUM(total) as total')
            ->where('paid', '=', true)
            ->get()
            ->toArray();

        $farm_names = array_map(function ($item) {
            return $item['farm']['name'];
        }, $data);

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => array_column($data, 'total'),
            'labels' => $farm_names,
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
        ];
    }
}
