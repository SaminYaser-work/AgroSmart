<?php

namespace App\Filament\Resources\SalaryResource\Widgets;

use App\Http\Controllers\SalaryController;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class SalaryStats extends BaseWidget
{

    private SalaryController $salaryController;
    protected static ?string $pollingInterval = null;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->salaryController = new SalaryController();
    }

    protected int | string | array $columnSpan = 2;

    protected function getCards(): array
    {

        $salary_due = money($this->salaryController->getSalaryDue(), 'bdt');

        $total_salary_paid = money($this->salaryController->getTotalSalaryPaid(), 'bdt');

        return [
            Card::make('Salary Due', $salary_due)
                ->icon('heroicon-o-currency-bangladeshi'),
            Card::make('Total Salary Paid', $total_salary_paid)
                ->icon('heroicon-o-currency-bangladeshi')
        ];
    }
}
