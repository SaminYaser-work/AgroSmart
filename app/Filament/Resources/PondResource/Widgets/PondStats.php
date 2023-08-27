<?php

namespace App\Filament\Resources\PondResource\Widgets;

use App\Models\Field;
use App\Models\Pond;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class PondStats extends BaseWidget
{

    protected int | string | array $columnSpan = 3;

    protected function getCards(): array
    {
        $totalPonds = Pond::count();
        $totalArea = Pond::sum('size');
        $totalAreaUsed = Pond::query()->whereNotNull('fish')->sum('size');
        $utilization = round($totalAreaUsed / $totalArea * 100, 2);
        $unusedPonds = Pond::query()->whereNull('fish')->count();


        return [
            Card::make('Total Ponds', $totalPonds)
                ->description($totalArea . ' m²')
                ->descriptionIcon('fas-layer-group'),
            Card::make('Pond Utilization', $utilization . '%' )
                ->description($totalAreaUsed . ' m² is in use')
                ->descriptionIcon('fas-layer-group'),
            Card::make('Unused Ponds', $unusedPonds)
                ->description('Out of ' . $totalPonds . ' ponds')
                ->descriptionIcon('fas-layer-group'),
        ];
    }
}
