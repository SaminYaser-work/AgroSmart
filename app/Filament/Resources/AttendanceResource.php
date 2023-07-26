<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Filament\Resources\AttendanceResource\Widgets\PresentTodayChart;
use App\Models\Attendance;
use App\Models\Farm;
use App\Models\Worker;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationGroup = 'HCM';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function getTableQuery(): Builder
    {
        return Attendance::with('worker');
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('worker.id')->label('ID')->sortable()->searchable(),
                TextColumn::make('worker_name')->label('Name')->searchable()
                ->getStateUsing(function (Attendance $record) {
                    return $record->worker->first_name . ' ' . $record->worker->last_name . '<br/>' . '<span class="text-xs text-slate-200">' . ucwords($record->worker->designation) . '</span>';
                })->html()
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('worker', function (Builder $query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }),
                TextColumn::make('date'),
                TextColumn::make('time_in')->placeholder('-'),
                TextColumn::make('time_out')->placeholder('-'),
                TextColumn::make('hours')->placeholder('-')
                ->getStateUsing(function (Attendance $record): float {
                    return round($record->getHoursWorked(), 2);
                }),
                TextColumn::make('leave_reason')->placeholder('-'),
            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('date')->default(Carbon::now()->toDateString())
                    ])
                    ->query(function (Builder $query, array $data) {
                        $qDate = Carbon::parse($data['date'])->toDateString();
                        $query->whereDate('date', $qDate);
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['date'] ?? null) {
                            $indicators['date'] = Carbon::parse($data['date'])->toFormattedDateString();
                        }

                        return $indicators;
                    })
            ])
            ->actions([
            ])
            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAttendances::route('/'),
        ];
    }
}
