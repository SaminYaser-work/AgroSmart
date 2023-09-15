<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use App\Http\Controllers\SalaryController;
use App\Models\Attendance;
use App\Models\Worker;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DailySalary extends BaseWidget
{
    protected static ?string $heading = 'Daily Salary';

    public Worker|null $record = null;
    private SalaryController $salaryController;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->salaryController = new SalaryController();
    }

    public function getTableRecordKey(Model $record): string
    {
        return uniqid();
    }

    protected function getTableQuery(): Builder
    {
        $id = $this->record->id;
        return $this->salaryController->getSalaryReportIndividual($id);
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('month')
                ->options([
                    1 => 'January',
                    2 => 'February',
                    3 => 'March',
                    4 => 'April',
                    5 => 'May',
                    6 => 'June',
                    7 => 'July',
                    8 => 'August',
                    9 => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December',
                ])
                ->placeholder('Select Month')
                ->query(function (Builder $query, array $data) {
                    $query->whereMonth('date', $data['value']);
                })
                ->default(date('m')),
//            Tables\Filters\SelectFilter::make('year')
//                ->options()
//                ->placeholder('Select Year')
//                ->default('2023')
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')->date()->sortable(),
            Tables\Columns\TextColumn::make('base')->sortable()->money('bdt', true),
            Tables\Columns\TextColumn::make('overtime')->sortable()->money('bdt', true),
            Tables\Columns\TextColumn::make('penalty')->sortable()->money('bdt', true),
            Tables\Columns\TextColumn::make('total')->sortable()->money('bdt', true),
        ];
    }
}
