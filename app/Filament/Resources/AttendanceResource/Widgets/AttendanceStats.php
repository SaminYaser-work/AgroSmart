<?php

namespace App\Filament\Resources\AttendanceResource\Widgets;

use App\Models\Attendance;
use App\Models\Worker;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class AttendanceStats extends BaseWidget
{

    private function getHoursWorkedCard() {
        $hoursToday = Attendance::query()
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->where('date', now()->format('Y-m-d'))
            ->selectRaw('TIMEDIFF(time_out, time_in) as hours')
            ->get()
            ->toArray();

        $totalEmployees = Worker::query()->count();

        $totalHours = array_reduce($hoursToday, function ($carry, $item) {
            return $carry + (int)explode(':', $item['hours'])[0];
        }, 0);

        $avgHours = round($totalHours / $totalEmployees, 2);

        $avgHoursCard = Card::make('Average Hours Worked', $avgHours);

        if ($avgHours < 8) {
            $avgHoursCard->color('danger')
                ->description($avgHours - 8 . ' hours less than the expected 8 hours')
                ->descriptionIcon('heroicon-s-trending-down');

        } else {
            $avgHoursCard->color('success')
                ->description($avgHours - 8 . ' hours more than the expected 8 hours')
                ->descriptionIcon('heroicon-s-trending-up');
        }

        return $avgHoursCard;
    }

    protected function getCards(): array
    {

        return [
            $this->getHoursWorkedCard()

//            Card::make('Unique views', '192.1k')
//                ->description('32k increase')
//                ->descriptionIcon('heroicon-s-trending-up')
//                ->color('success'),
//            Card::make('Unique views', '192.1k')
//                ->description('32k increase')
//                ->descriptionIcon('heroicon-s-trending-up')
//                ->color('success'),
        ];
    }
}
