<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Filament\Resources\AttendanceResource\Widgets\AttendanceStats;
use App\Filament\Resources\AttendanceResource\Widgets\LateTable;
use App\Filament\Resources\AttendanceResource\Widgets\PresentTodayChart;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Widgets\StatsOverviewWidget\Card;

class ManageAttendances extends ManageRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }

    protected function getFooterWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getFooterWidgets(): array
    {
        return [
            AttendanceResource\Widgets\OnTimeTable::class,
            LateTable::class
        ];
    }

    protected function getHeaderWidgets(): array {
        return [
            AttendanceStats::class,
            PresentTodayChart::class,
            AttendanceResource\Widgets\OnTimeTodayChart::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
