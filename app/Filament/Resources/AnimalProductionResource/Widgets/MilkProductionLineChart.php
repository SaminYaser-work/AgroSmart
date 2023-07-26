<?php

namespace App\Filament\Resources\AnimalProductionResource\Widgets;

use App\Models\AnimalProduction;
use App\Models\Farm;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MilkProductionLineChart extends ApexChartWidget
{
    protected static string $chartId = 'milkProductionLineChart';
    protected static ?string $heading = 'Daily Milk Production';
    protected static ?string $pollingInterval = null;
    protected static bool $deferLoading = true;
    private $year_months;

    protected int | string | array $columnSpan = 4;

    public function __construct()
    {
        parent::__construct();

        $this->year_months = AnimalProduction::query()
            ->where('type', '=', 'Milk')
            ->distinct()
            ->selectRaw('EXTRACT( YEAR_MONTH FROM `date` ) as date')
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();

        $this->year_months = array_map(function ($year_month) {
            return Carbon::createFromFormat('Ym', $year_month['date'])->format('F Y');
        }, $this->year_months);
    }

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }

    protected function getFormSchema(): array
    {
        $farms = Farm::all()->pluck('name', 'id');

        return [
            Select::make('farm')
                ->placeholder('All')
                ->label('Farm')
                ->options($farms),
            Select::make('year_month')
                ->label('Month')
                ->options($this->year_months)
                ->default(array_key_first($this->year_months))
        ];
    }

    protected function getOptions(): array
    {

        if (!$this->readyToLoad) {
            return [];
        }

        $farm_id = $this->filterFormData['farm'];
        $index = $this->filterFormData['year_month'];
        $ym = $this->year_months[$index];
        $start = Carbon::createFromFormat('F Y', $ym)->startOfMonth()->toDateString();
        $end = Carbon::createFromFormat('F Y', $ym)->endOfMonth()->toDateString();

        $data = AnimalProduction::whereHas('farm', function ($query) use ($farm_id) {
            $query->when($farm_id, function ($query, $farm_id) {
                $query->where('id', $farm_id);
            });
        })
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->groupBy('date')
            ->selectRaw('date, sum(quantity) as quantity');


        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'MilkProductionLineChart',
                    'data' => $data->pluck('quantity'),
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'xaxis' => [
                'categories' => array_map(function ($date) {
                    return Carbon::parse($date)->format('d');
                }, $data->pluck('date')->toArray()),
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
                'title' => [
                    'text' => 'Litres'
                ]
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
//                    'shade' => 'dark',
//                    'gradientToColors' => ['blue'],
                    'shadeIntensity' => 1,
//                    'type' => 'horizontal',
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.9,
                    'stops' => [0, 90, 100]
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'markers' => [
                'size' => 0,
            ],
        ];
    }
}
