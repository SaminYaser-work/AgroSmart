<?php

namespace App\Filament\Resources\PondWeeklyReportResource\Pages;

use App\Filament\Resources\PondWeeklyReportResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPondWeeklyReport extends EditRecord
{
    protected static string $resource = PondWeeklyReportResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
