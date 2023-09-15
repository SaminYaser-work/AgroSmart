<?php

namespace App\Filament\Resources\PondWeeklyReportResource\Widgets;

use App\Models\PondWeeklyReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Str;

class PondWeeklyReportStats extends BaseWidget
{

    protected function getCards(): array
    {
        $cards = [];
        $data = $this->getData();

        foreach ($data as $key => $value) {
            $cards[] = Card::make('Average ' . $value['name'], $value['curr'])
                ->description($value['desc'])
                ->descriptionIcon($value['icon'])
                ->chart($value['chart'])
                ->color($value['color']);
        }

        return $cards;
    }

    private function getData()
    {
        $weeks = PondWeeklyReport::query()->distinct('date')->orderBy('date', 'desc')->limit(2)->pluck('date')->toArray();

        $columnNames = [
            'production',
            'yield',
            'survival_rate',
            'average_weight',
            'average_growth',
            'dissolved_oxygen',
            'water_level',
            'water_temperature',
            'ph',
            'turbidity',
            'ammonia',
            'nitrate',
        ];

        $currWeekAvgData = PondWeeklyReport::query()
            ->where('date', $weeks[0])
            ->selectRaw('AVG(production) as production, AVG(yield) as yield, AVG(survival_rate) as survival_rate, AVG(average_weight) as average_weight, AVG(average_growth) as average_growth, AVG(dissolved_oxygen) as dissolved_oxygen, AVG(water_level) as water_level, AVG(water_temperature) as water_temperature, AVG(ph) as ph, AVG(turbidity) as turbidity, AVG(ammonia) as ammonia, AVG(nitrate) as nitrate')
            ->first()
            ->toArray();

        $currWeekData = PondWeeklyReport::query()
            ->where('date', $weeks[0])
            ->get()
            ->toArray();

//        dd($currWeekData);

        $prevWeekAvgData = PondWeeklyReport::query()
            ->where('date', $weeks[1])
            ->selectRaw('AVG(production) as production, AVG(yield) as yield, AVG(survival_rate) as survival_rate, AVG(average_weight) as average_weight, AVG(average_growth) as average_growth, AVG(dissolved_oxygen) as dissolved_oxygen, AVG(water_level) as water_level, AVG(water_temperature) as water_temperature, AVG(ph) as ph, AVG(turbidity) as turbidity, AVG(ammonia) as ammonia, AVG(nitrate) as nitrate')
            ->first()
            ->toArray();

        return array_map(function ($curr, $prev, $columnName) use ($currWeekData) {
            $diff = $curr - $prev;
            $icon = $diff > 0 ? 'fas-arrow-trend-up' : 'fas-arrow-trend-down';
            $desc = ($diff > 0 ? 'Increased' : 'Decreased') . ' by ' . abs(round($diff, 2)) . ' from previous week';
            return [
                'name' => Str::of($columnName)->snake()->replace('_', ' ')->title(),
                'curr' => round($curr, 2),
                'desc' => $desc,
                'icon' => $icon,
                'color' => $diff > 0 ? 'success' : 'danger',
                'chart' => array_map(function ($data) use ($columnName) {
                    return $data[$columnName];
                }, $currWeekData),
            ];
        }, $currWeekAvgData, $prevWeekAvgData, $columnNames);
    }
}
