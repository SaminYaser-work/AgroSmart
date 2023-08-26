<?php

namespace App\Filament\Resources\PondWeeklyReportResource\Pages;

use App\Filament\Resources\PondWeeklyReportResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPondWeeklyReports extends ListRecords
{
    protected static string $resource = PondWeeklyReportResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            PondWeeklyReportResource\Widgets\PondWeeklyReportStats::class
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
