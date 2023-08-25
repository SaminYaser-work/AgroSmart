<?php

namespace App\Filament\Resources\CropProjectResource\Widgets;

use App\Models\Attendance;
use App\Models\CropProject;
use App\Models\Field;
use Filament\Widgets\DoughnutChartWidget;
use Illuminate\Contracts\View\View;

class CropStages extends DoughnutChartWidget
{
    protected static ?string $heading = 'Crop Stages';

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = null;

    protected static bool $deferLoading = true;

    protected function getLoadingIndicator(): null|string|View
    {
        return view('loading');
    }

    protected function getData(): array
    {

        $data = CropProject::query()
            ->groupBy('status')
            ->selectRaw('count(*) as count, status')
            ->get()
            ->toArray();

        return [
            'datasets' => [
                [
                    'data' => array_column($data, 'count'),
                    'backgroundColor' => ['rgb(54, 162, 235)', 'rgb(255, 99, 132)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)', 'rgb(201, 203, 207)']
                ],
            ],
            'labels' => array_column($data, 'status'),
        ];
    }
}
