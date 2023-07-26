<?php

namespace App\Filament\Resources\SalaryResource\Pages;

use App\Filament\Resources\SalaryResource;
use Filament\Resources\Pages\ManageRecords;

class ManageSalaries extends ManageRecords
{
    protected static string $resource = SalaryResource::class;
    protected static ?string $title = 'Payroll';
    protected static ?string $navigationLabel = 'Payroll';

    protected function getHeaderWidgets(): array
    {
        return [
            SalaryResource\Widgets\SalaryStats::class,
            SalaryResource\Widgets\SalaryCompByMonth::class,
            SalaryResource\Widgets\SalaryCompByFarm::class
        ];
    }

    protected function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
