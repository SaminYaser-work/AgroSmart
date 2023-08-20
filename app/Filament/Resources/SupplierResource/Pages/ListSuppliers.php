<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            SupplierResource\Widgets\SupplierStats::class,
            SupplierResource\Widgets\SupplierTypeChart::class
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add New Supplier'),
        ];
    }
}
