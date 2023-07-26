<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\AttendanceResource\Widgets\LateTable;
use App\Filament\Resources\WorkerResource;
use App\Models\Worker;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditWorker extends EditRecord
{
    protected static string $resource = WorkerResource::class;

    protected static ?string $title = 'Worker Profile';

//    public function getRecordTitle(): string|Htmlable
//    {
//        return $this->record->name . '\'s Profile';
//    }

//    protected function getTitle(): string|Htmlable
//    {
//        return $this->record->name . '\'s Profile';
//    }

    protected function getHeaderWidgetsColumns(): int|string|array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WorkerResource\Widgets\IndividualWorkerHourBarChart::class,
            WorkerResource\Widgets\IndividualWorkerAttendanceTable::class,
            WorkerResource\Widgets\DailySalary::class
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
