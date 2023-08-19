<?php

namespace App\Filament\Resources\FieldResource\Widgets;

use App\Models\Field;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class FieldStats extends BaseWidget
{
    protected function getCards(): array
    {
        $totalFields = Field::count();
        $totalArea = Field::sum('area');
        $totalAreaUsed = Field::query()->where('status', 0)->sum('area');
        $utilization = round($totalAreaUsed / $totalArea * 100, 2);


        return [
            Card::make('Total Fields', $totalFields)
                ->description($totalArea . ' ha')
                ->descriptionIcon('fas-layer-group'),
            Card::make('Area Utilization', $utilization . '%' )
                ->description($totalAreaUsed . ' ha is in use')
                ->descriptionIcon('fas-layer-group'),
        ];
    }
}
