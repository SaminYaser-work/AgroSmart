<?php

namespace App\Filament\Resources\SupplierResource\Widgets;

use App\Models\Field;
use App\Models\Supplier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class SupplierStats extends BaseWidget
{
    protected function getCards(): array
    {
        $totalSuppliers = Supplier::count();
        $avgLeadTime = round(Supplier::avg('lead_time'), 1);


        return [
            Card::make('Total Suppliers', $totalSuppliers)
                ->descriptionIcon('fas-layer-group'),
            Card::make('Average Lead Time', $avgLeadTime . ' Days' )
                ->descriptionIcon('fas-layer-group'),
        ];
    }
}
