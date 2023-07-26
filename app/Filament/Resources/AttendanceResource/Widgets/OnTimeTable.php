<?php

namespace App\Filament\Resources\AttendanceResource\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class OnTimeTable extends BaseWidget
{
    protected static ?string $heading = 'On Time Today';

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50];
    }

    protected function getTableQuery(): Builder
    {
        return Attendance::query()
            ->where('date', now()->format('Y-m-d'))
            ->where('time_in', '<=', '08:00:00');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('worker_name')
                ->label('Name')
                ->searchable()
                ->sortable()
                ->getStateUsing(function (Attendance $record) {
                    return $record->worker->first_name . ' ' . $record->worker->last_name . '<br/>' . '<span class="text-xs text-slate-200">' . ucwords($record->worker->designation) . '</span>';
                })->html(),
            Tables\Columns\TextColumn::make('time_in')
                ->getStateUsing(function (Attendance $record) {
                    return Carbon::parse($record->time_in)->format('h:i:s A');
                }),
            Tables\Columns\TextColumn::make('early_by')
                ->label('Early By')
                ->getStateUsing(function (Attendance $record) {
                    $timeIn = Carbon::parse($record->time_in);
                    $shiftStart = Carbon::parse('08:00:00');
                    return $shiftStart->diff($timeIn)->format('%H:%I:%S');
                })
        ];
    }
}
