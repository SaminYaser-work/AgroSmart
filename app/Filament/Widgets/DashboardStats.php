<?php

namespace App\Filament\Widgets;

use App\Models\Animal;
use App\Models\Customer;
use App\Models\Farm;
use App\Models\Field;
use App\Models\Pond;
use App\Models\Storage;
use App\Models\Worker;
use App\Utils\Enums;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\HtmlString;

class DashboardStats extends BaseWidget
{
    protected int | string | array $columnSpan = 4;
    protected static ?string $pollingInterval = null;
    protected function getCards(): array
    {
        $totalFarms = Farm::count();

        $totalFields = Field::count();
        $totalArea = Field::sum('area');

        $totalPonds = Pond::count();
        $totalPondArea = Pond::sum('size');

        $totalLivestock = Animal::count();
        $totalBarns = Storage::query()->where('type', '=', 'Barn')->count();

        $totalWorkers = Worker::count();
        $totalDepartments = Worker::query()->distinct('designation')->count();

        $totalCustomers = Customer::count();
        $totalProducts = count(Enums::$SaleItem);


        return [
            Card::make('Farms', $totalFarms)
                ->chart(self::getRandomNumberArray())
                ->description('Total Farms')
                ->color('primary')
                ->descriptionIcon('fas-wheat-awn'),

            Card::make('Fields', $totalFields)
                ->chart(self::getRandomNumberArray())
                ->color('primary')
                ->description($totalArea . ' acres of land')
                ->descriptionIcon('fas-sun-plant-wilt'),

            Card::make('Ponds', $totalPonds)
                ->chart(self::getRandomNumberArray())
                ->color('primary')
                ->description($totalPondArea . ' Sq. metre of water body')
                ->descriptionIcon('fas-water'),

            Card::make('Livestock', $totalLivestock)
                ->chart(self::getRandomNumberArray())
                ->description($totalBarns . ' barns')
                ->color('primary')
                ->descriptionIcon('fas-cow'),

            Card::make('Workers', $totalWorkers)
                ->chart(self::getRandomNumberArray())
                ->description('Over ' . $totalDepartments . ' departments')
                ->color('primary')
                ->descriptionIcon('fas-person-digging'),

            Card::make('Customers', $totalCustomers)
                ->chart(self::getRandomNumberArray())
                ->description('Ordering ' . $totalProducts . ' products')
                ->color('primary')
                ->descriptionIcon('fas-person-digging'),
        ];
    }

    private static function getRandomNumberArray(): array {
        return array_map(function () {
            return rand(0, 100);
        }, array_fill(0, 10, null));
    }
}
