<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use App\Models\Attendance;
use App\Models\Worker;
use Carbon\Carbon;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class IndividualWorkerAttendanceTable extends BaseWidget
{

    protected static ?string $heading = 'Attendance Table';

    public Worker|null $record = null;

    protected function getTableQuery(): Builder
    {
        return Attendance::query()
            ->where('worker_id', $this->record->id);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')->date()->sortable()->searchable(),
            Tables\Columns\TextColumn::make('time_in')->default('--'),
            Tables\Columns\TextColumn::make('time_out')->default('--'),
            Tables\Columns\TextColumn::make('hours')
                ->getStateUsing(function ($record) {
                    $time_in = Carbon::parse($record->time_in);
                    $time_out = Carbon::parse($record->time_out);
                    $hours = $time_in->diff($time_out)->format('%h:%i:%s');
                    return $hours;
                })
        ];
    }
}
