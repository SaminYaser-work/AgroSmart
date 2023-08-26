<?php

namespace App\Filament\Resources\PondResource\Widgets;

use App\Models\Field;
use App\Models\Pond;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class PondStats extends BaseWidget
{
    protected function getCards(): array
    {
        $totalPonds = Pond::count();
        $totalArea = Pond::sum('size');
        $totalAreaUsed = Pond::query()->whereNotNull('fish')->sum('size');
        $utilization = round($totalAreaUsed / $totalArea * 100, 2);


        return [
            Card::make('Total Ponds', $totalPonds)
                ->description($totalArea . ' m²')
                ->descriptionIcon('fas-layer-group'),
            Card::make('Area Utilization', $utilization . '%' )
                ->description($totalAreaUsed . ' m² is in use')
                ->descriptionIcon('fas-layer-group'),
        ];
    }
}
