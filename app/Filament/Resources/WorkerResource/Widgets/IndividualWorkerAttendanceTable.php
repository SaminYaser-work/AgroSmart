<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use App\Models\Attendance;
use App\Models\Worker;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

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
            Tables\Columns\TextColumn::make('time_in')
                ->default('Missing')
                ->color(function (Attendance $record) {
                    return ($record->time_in) ? '' : 'danger';
                }),
            Tables\Columns\TextColumn::make('time_out')
                ->default('Missing')
                ->color(function (Attendance $record) {
                    return ($record->time_in) ? '' : 'danger';
                }),
            Tables\Columns\TextColumn::make('hours')
                ->alignCenter()
                ->label('Hours Worked')
                ->getStateUsing(function (Attendance $record) {
                    $time_in = Carbon::parse($record->time_in);
                    $time_out = Carbon::parse($record->time_out);
                    $hours = $time_in->diff($time_out)->format('%H:%I:%S');
                    if ($hours == '00:00:00') {
                        return '--';
                    }
                    return $hours;
                })
                ->color(function (Attendance $record) {
                    if ($record->time_in == null || $record->time_out == null) {
                        return 'danger';
                    }
                    $time_in = Carbon::parse($record->time_in);
                    $time_out = Carbon::parse($record->time_out);
                    $hours = $time_in->diffInHours($time_out);
                    return ($hours > $this->record->expected_hours) ? '' : 'danger';
                }),
        ];
    }
}
