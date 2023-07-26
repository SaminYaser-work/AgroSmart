<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryResource\Pages;
use App\Filament\Resources\SalaryResource\RelationManagers;
use App\Models\Salary;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SalaryResource extends Resource
{
    protected static ?string $model = Salary::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-bangladeshi';
    protected static ?string $navigationGroup = 'HCM';

//    public array $data_list= [
//        'calc_columns' => [
//            'total',
//        ],
//    ];

    public static function form(Form $form): \Filament\Resources\Form
    {
        return $form
            ->schema([
//                Forms\Components\Select::make('worker_id')
//                    ->relationship('worker', 'id')
//                    ->required(),
//                Forms\Components\Select::make('farm_id')
//                    ->relationship('farm', 'name')
//                    ->required(),
//                Forms\Components\TextInput::make('month')
//                    ->required()
//                    ->maxLength(255),
//                Forms\Components\TextInput::make('year')
//                    ->required()
//                    ->maxLength(255),
//                Forms\Components\TextInput::make('base')
//                    ->required(),
//                Forms\Components\TextInput::make('overtime')
//                    ->required(),
//                Forms\Components\TextInput::make('bonus')
//                    ->required(),
            ]);
    }

//    public static function getEloquentQuery(): Builder
//    {
//        return Salary::where('paid', '=', false);
//    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(fn($record, $column) => $record->worker->first_name . ' ' . $record->worker->last_name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('farm.name'),
                Tables\Columns\TextColumn::make('month')
                    ->getStateUsing(fn($record, $column) => date('F', mktime(0, 0, 0, $record->month, 10))),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('base')->sortable()->money('bdt'),
                Tables\Columns\TextColumn::make('overtime')->sortable()->money('bdt'),
                Tables\Columns\TextColumn::make('penalty')->sortable()->money('bdt'),
                Tables\Columns\TextColumn::make('bonus')->sortable()->money('bdt'),
                Tables\Columns\TextColumn::make('total')->sortable()->money('bdt'),
                Tables\Columns\IconColumn::make('paid')->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
            ])
            ->defaultSort('month', 'desc')
            ->filters([
                Filter::make('paid')->label('Unpaid')
                ->query(fn (Builder $query): Builder => $query->where('paid', '=', false))
                ->default(true),
            ])
            ->actions([
                Action::make('pay')
                    ->label('Pay')
                    ->icon('heroicon-o-currency-bangladeshi')
                    ->action(function (Salary $record) {
                        $record->update([
                            'paid' => true,
                        ]);
                        redirect("/salaries");
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Pay Salary')
                    ->modalSubheading(function (Salary $record) {
                        $date = date('F', mktime(0, 0, 0, $record->month, 10)) . ', ' . $record->year;
                        return "Are you sure you want to pay {$record->worker->first_name} {$record->worker->last_name}'s salary for {$date}?";
                    })
                    ->modalButton('Pay')
                    ->disabled(fn($record) => $record->paid)
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('pay_bulk')
                    ->label('Pay')
                    ->icon('heroicon-o-currency-bangladeshi')
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $record->update([
                                'paid' => true,
                            ]);
                        });
                        redirect("/salaries");
                    })
                    ->modalHeading('Pay Salary')
                    ->modalSubheading(function (Collection $records) {
                        $date = date('F', mktime(0, 0, 0, $records->first()->month, 10)) . ', ' . $records->first()->year;
                        return "Are you sure you want to pay the salary of {$records->count()} people for {$date}?";
                    })
                    ->modalButton('Pay')
                    ->disabled(fn(Collection $records) => $records->every(fn($record) => $record->paid))
                    ->deselectRecordsAfterCompletion()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSalaries::route('/'),
        ];
    }

//    protected function getTableContentFooter(): ?View
//    {
//        return view('table.footer', $this->data_list);
//    }
}
