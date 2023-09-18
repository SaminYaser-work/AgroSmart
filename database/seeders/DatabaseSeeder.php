<?php

namespace Database\Seeders;

use App\Http\Controllers\SalaryController;
use App\Models\Animal;
use App\Models\AnimalExpense;
use App\Models\AnimalProduction;
use App\Models\Attendance;
use App\Models\CropProject;
use App\Models\Customer;
use App\Models\Farm;
use App\Models\FarmingExpenses;
use App\Models\Field;
use App\Models\FishExpenses;
use App\Models\Inventory;
use App\Models\OtherExpenses;
use App\Models\Pond;
use App\Models\PondMetrics;
use App\Models\PondWeeklyReport;
use App\Models\PurchaseOrder;
use App\Models\Salary;
use App\Models\SalesOrder;
use App\Models\Storage;
use App\Models\StorageExpenses;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Worker;
use App\Utils\Enums;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    private int $months = 3;
    private \Carbon\CarbonPeriod $period;
    private Carbon $end_date;
    private Carbon $start_date;

    private int $batch_insert_limit = 5000;

    private SalaryController $salaryController;

    public function __construct()
    {
        $this->start_date = Carbon::now()->endOfMonth()->subMonths($this->months);
        $this->end_date = Carbon::now()->endOfMonth();
        $this->period = Carbon::parse($this->start_date)->daysUntil($this->end_date);
        $this->salaryController = new SalaryController();
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        \Log::debug('Seeding User');
        User::factory(1)->create();

        \Log::debug('Seeding Farms, Workers & Field');
        Farm::factory(3)
            ->has(
                Worker::factory()->count(15)
            )
            ->has(
                Field::factory()->count(5)
            )
            ->create();

        \Log::debug('Seeding Customers & Sales Order');
        Customer::factory(30)
            ->has(
                SalesOrder::factory()->count(10)
            )
            ->create();

        \Log::debug('Seeding Suppliers');
        Supplier::factory(30)->create();
        $this->seedPurchaseOrders();

        $this->seedAttendances();
        $this->seedStorage();
        $this->seedAnimals();
        $this->seedAnimalProduction();
        $this->seedSuppliers();
        $this->seedExtraSalesOrder();
        $this->seedPurchaseOrders();
        $this->seedSalaries();
        $this->seedFields();
        $this->seedCropProjects();
        $this->seedInventory();
        $this->seedPonds();
        $this->seedAnimalExpense();
        $this->seedFarmingExpense();
        $this->seedFishExpense();
        $this->seedStorageExpense();
        $this->seedOtherExpense();

        \Log::debug('Seeding Done');
    }

    private function seedPurchaseOrders(): void
    {
        \Log::debug('Seeding Purchase Orders');
        $suppliers = Supplier::all();
        $rows = [];
        foreach ($suppliers as $supplier) {
            foreach (range(0, 10) as $i) {
                $order_date = \Illuminate\Support\Carbon::now()->subDays(rand(1, 30));
                $expected_delivery_date = Carbon::parse($order_date)->addDays(rand(1, 15));
                $actual_delivery_date = null; // Not delivered yet

                if (fake()->boolean(40)) {
                    $actual_delivery_date = Carbon::parse($expected_delivery_date)->subDays(rand(0, 1)); // On time
                } elseif (fake()->boolean(10)) {
                    $actual_delivery_date = Carbon::parse($expected_delivery_date)->addDays(rand(1, 7)); // late
                }

                $quantity = fake()->numberBetween(1, 100);
                $unit_price = fake()->randomFloat(2, 100, 2000);
                $amount = $quantity * $unit_price;

                [$products, $unit] = Enums::getSupplierProducts($supplier->type);

                $data = [
                    'name' => fake()->randomElement($products),
                    'type' => $supplier->type,
                    'order_date' => $order_date,
                    'expected_delivery_date' => $expected_delivery_date,
                    'actual_delivery_date' => $actual_delivery_date,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'amount' => $amount,
                    'unit' => $unit,
                    'supplier_id' => $supplier->id,
                    'farm_id' => fake()->randomElement(Farm::all()->pluck('id')->toArray())
                ];

                $rows[] = $data;
            }
        }

        PurchaseOrder::query()->insert($rows);

        foreach ($suppliers as $supplier) {
            //  Update lead time
            $avgLeadTime = PurchaseOrder::query()
//                ->select(\DB::raw('AVG(TIMESTAMPDIFF(DAY, order_date, actual_delivery_date)) as avg_lead_time')) // MySQL Version
                ->select(\DB::raw('AVG(julianday(order_date) - julianday(actual_delivery_date)) as avg_lead_time')) // SQLite Version
                ->whereNotNull('actual_delivery_date')
                ->where('supplier_id', '=', $supplier->id)
                ->pluck('avg_lead_time')
                ->first();
            \Log::info('Average Lead Time: ' . $avgLeadTime);
            Supplier::query()->where('id', '=', $supplier->id)->update(['lead_time' => $avgLeadTime]);
        }
    }

    private function seedAttendances(): void
    {
        \Log::debug('Seeding Attendances');
        $rows = [];
        $workers = Worker::all();
        foreach ($workers as $worker) {
            foreach ($this->period as $date) {
                $data = [
                    'date' => $date->format('Y-m-d'),
                    'worker_id' => $worker->id,
                ];

                if (fake()->numberBetween(0, 100) > 5) {
                    $data['time_in'] = fake()->dateTimeBetween('06:00:00', '09:00:00')->format('H:i:s');
                    $data['time_out'] = fake()->dateTimeBetween('18:00:00', '20:00:00')->format('H:i:s');
                    $data['leave_reason'] = null;
                } else {
                    $data['time_in'] = null;
                    $data['time_out'] = null;
                    $data['leave_reason'] = fake()->sentence();
                }

                $rows[] = $data;
            }
        }
        Attendance::query()->insert($rows);
    }

    private function seedStorage(): void
    {
        \Log::debug('Seeding Storage');
        $farms = Farm::all();
        $rows = [];
        foreach ($farms as $farm) {
            for ($i = 0; $i < 10; $i++) {
                $max_capacity = fake()->numberBetween(100, 1000);
                $current_capacity = fake()->numberBetween(0, $max_capacity);
                $data = [
                    'name' => 'ST-' . $farm->id . '-' . fake()->buildingNumber(),
                    'type' => fake()->randomElement(Enums::$StorageType),
                    'capacity' => $max_capacity,
                    'current_capacity' => $current_capacity,
                    'unit' => 'unit',
                    'farm_id' => $farm->id,
                ];

                $rows[] = $data;
            }
        }
        Storage::query()->insert($rows);
    }

    private function seedAnimals(): void
    {
        \Log::debug('Seeding Animals');
        $storages = Storage::query()->where('type', '=', 'Barn')->get();
        $rows = [];
        foreach ($storages as $storage) {
            for ($i = 0; $i < 10; $i++) {
                $data = [
                    'name' => fake()->firstNameFemale(),
                    'type' => 'Cow',
                    'breed' => fake()->randomElement(Enums::$CowBreed),
                    'color' => fake()->colorName(),
                    'gender' => 'female',
                    'storage_id' => $storage->id,
                    'farm_id' => $storage->farm_id,
                ];
                $rows[] = $data;
            }
        }
        Animal::query()->insert($rows);
    }

    private function seedAnimalProduction(): void
    {
        \Log::debug('Seeding Animal Production');
        $rows = [];
        $animals = Animal::query()->where('type', '=', 'Cow')->get();
        foreach ($animals as $animal) {
            foreach ($this->period as $date) {
                $data = [
                    'type' => 'Milk',
                    'date' => $date->format('Y-m-d'),
                    'quantity' => fake()->numberBetween(1, 10),
                    'unit' => 'litre',
                    'animal_id' => $animal->id,
                    'farm_id' => $animal->farm_id,
                ];
                $rows[] = $data;

                if (count($rows) > 1000) {
                    AnimalProduction::query()->insert($rows);
                    $rows = [];
                }
            }
        }
        AnimalProduction::query()->insert($rows);
    }

    private function seedSuppliers(): void
    {
        \Log::debug('Seeding Suppliers');
        $rows = [];
        foreach (Enums::$SupplierType as $supplierType) {
            for ($i = 0; $i < 10; $i++) {
                $data = [
                    'name' => fake()->company(),
                    'type' => $supplierType,
                    'address' => fake()->address(),
                    'phone' => fake()->phoneNumber(),
                    'email' => fake()->email(),
                    'lead_time' => fake()->numberBetween(1, 10),
                ];
                $rows[] = $data;
            }
        }
        Supplier::query()->insert($rows);
    }

    private function seedExtraSalesOrder(): void
    {

        \Log::debug('Seeding Extra Sales Order');
        $days = Carbon::now()->startOfYear()->daysUntil(Carbon::now()->endOfMonth()->subMonth());
        $customer_ids = Customer::all()->pluck('id')->toArray();
        $farm_ids = Farm::all()->pluck('id')->toArray();
        $rows = [];

        foreach ($days as $day) {
            foreach (range(0, rand(5, 15)) as $_) {
                $order_date = $day;
                $expected_delivery_date = $day->copy()->addDays(3);
                $actual_delivery_date = $expected_delivery_date->copy();

                $quantity = fake()->numberBetween(1, 100);
                $unit_price = fake()->randomFloat(2, 100, 2000);
                $amount = $quantity * $unit_price;

                $type = fake()->randomElement(Enums::$ItemType);
                $unit = $type === 'Dairy' ? 'litre' : 'kg';
                $data = [
                    'name' => fake()->randomElement(Enums::$SaleItem),
                    'type' => $type,
                    'order_date' => $order_date,
                    'expected_delivery_date' => $expected_delivery_date->toDateString(),
                    'actual_delivery_date' => $actual_delivery_date->toDateString(),
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'amount' => $amount,
                    'unit' => $unit,
                    'customer_id' => fake()->randomElement($customer_ids),
                    'farm_id' => fake()->randomElement($farm_ids)
                ];
                $rows[] = $data;
            }
        }

        SalesOrder::query()->insert($rows);
    }

    private function seedSalaries(): void
    {
        \Log::debug('Seeding Salaries');
        $now = Carbon::now();
        $rows = [];
        $workers = Worker::all();
        foreach ($workers as $worker) {
            for ($i = 0; $i < $this->months; $i++) {
                $month = $now->copy()->subMonths($i);

                $res = $this->salaryController->getSalaryReportIndividual($worker->id)
                    ->whereMonth('date', '=', $month->month)
                    ->whereYear('date', '=', $month->year)
                    ->get()->toArray();

                $total = array_sum(array_column($res, 'total'));
                $base = array_sum(array_column($res, 'base'));
                $overtime = array_sum(array_column($res, 'overtime'));
                $penalty = array_sum(array_column($res, 'penalty'));
                $bonus = 0;
                if ($i % 2 === 0) {
                    $bonus = 1000;
                }

                $isPaid = !($month->month === $now->month && $month->year === $now->year);

                $data = [
                    'worker_id' => $worker->id,
                    'farm_id' => $worker->farm_id,
                    'month' => $month->month,
                    'year' => $month->year,
                    'base' => $base,
                    'overtime' => $overtime,
                    'penalty' => $penalty,
                    'bonus' => $bonus,
                    'total' => $total + $bonus,
                    'paid' => $isPaid
                ];

                $rows[] = $data;
            }
        }

        Salary::query()->insert($rows);
    }

    private function seedFields(): void
    {
        \Log::debug('Seeding Fields');
        $rows = [];
        $farms = Farm::all();
        foreach ($farms as $farm) {
            for ($i = 0; $i < 10; $i++) {
                $data = [
                    'address' => fake()->address(),
                    'area' => fake()->numberBetween(10, 100),
                    'name' => fake()->firstNameFemale(),
                    'soil_type' => fake()->randomElement(Enums::$SoilType),
                    'status' => true,
                    'farm_id' => $farm->id,
                ];
                $rows[] = $data;
            }
        }
        Field::query()->insert($rows);
    }

    private function seedCropProjects(): void
    {
        \Log::debug('Seeding Crop Projects');

        $totalFields = Field::count();

        $rows = [];

        if ($totalFields === 0) {
            return;
        }

        $randomFields = $this->getRandomSamples(Field::all()->toArray(), $totalFields - 5);

        foreach ($randomFields as $field) {
            $randomCropName = fake()->randomElement(Enums::$CropName);
            $cropYieldPerHa = Enums::getExpectedYield($randomCropName);
            $startDate = Carbon::now()->subDays(rand(10, 70))->toDateString();
            $cropEndDate = Carbon::parse($startDate)->addDays(Enums::getExpectedEndDateInDays($randomCropName))->toDateString();

            $crop = new CropProject();
            $data = [
                'crop_name' => fake()->randomElement(Enums::$CropName),
                'start_date' => $startDate,
                'end_date' => null,
                'expected_end_date' => $cropEndDate,
                'status' => fake()->randomElement(array_slice(Enums::$CropStage, 0, 4)),
                'yield' => 0.0,
                'expected_yield' => $cropYieldPerHa * $field['area'],
                'field_id' => $field['id'],
                'farm_id' => $field['farm_id']
            ];
            $rows[] = $data;
            Field::query()->where('id', '=', $field['id'])->update(['status' => false]);
        }
        CropProject::query()->insert($rows);
    }

    private function getRandomSamples(array $array, int $count): array
    {
        $keys = array_rand($array, $count);
        $result = [];
        foreach ($keys as $key) {
            $result[] = $array[$key];
        }
        return $result;
    }

    private function seedInventory(): void
    {
        \Log::debug('Seeding Inventory');
        $rows = [];
        $orders = PurchaseOrder::query()->whereNotNull('actual_delivery_date')->get();

        foreach ($orders as $order) {
            $isOperational = fake()->boolean(85);
            $reasonForFailure = null;
            if (!$isOperational) {
                $reasonForFailure = fake()->sentence();
            }

            [$storageType, $type] = Enums::getStorage($order->type);

            $st = Storage::query()
                ->where('type', '=', $storageType)
                ->where('farm_id', '=', $order->farm_id)
                ->pluck('id');

            if (empty($st)) {
                continue;
            }

            $storage_id = fake()->randomElement($st);

            $data = [
                'name' => $order->name,
                'type' => $order->type,
                'is_operational' => $isOperational,
                'reason_for_failure' => $reasonForFailure,
                'buying_price' => $order->amount,
                'yearly_depreciation' => $order->amount * rand(1, 10) / 100,
                'farm_id' => $order->farm_id,
                'supplier_id' => $order->supplier_id,
                'purchase_order_id' => $order->id,
                'storage_id' => $storage_id,
            ];

            $rows[] = $data;
        }

        Inventory::query()->insert($rows);
    }

    private function seedPonds(): void
    {
        \Log::debug('Seeding Ponds');

        $metrics_path = storage_path('pond.json');
        $metrics = json_decode(file_get_contents($metrics_path), true);
        $len = count($metrics["ph"]);

        $farms = Farm::all();

        $pond_rows = [];
        $pond_metrics_rows = [];
        $pond_weekly_report_rows = [];

        foreach ($farms as $farm) {
            foreach (range(0, 10) as $_) {
                $data = [
                    'name' => fake()->streetName(),
                    'pond_type' => Enums::$PondType[0],
                    'water_type' => fake()->randomElement(Enums::$WaterType),
                    'fish' => fake()->boolean(70) ? fake()->randomElement(Enums::$FishName) : null,
                    'size' => fake()->randomFloat(2, 50, 100),
                    'farm_id' => $farm->id,
                ];

                $pond_rows[] = $data;
            }
        }

        Pond::query()->insert($pond_rows);

        $ponds = Pond::all();

        foreach ($ponds as $pond) {
            $r = rand(0, $len - 1);
            $metrics_data = [
                'water_temperature' => $metrics["temperature"][$r],
                'ph' => $metrics["ph"][$r],
                'turbidity' => $metrics["turbidity"][$r],
                'farm_id' => $pond->farm_id,
                'pond_id' => $pond->id,
            ];

            $pond_metrics_rows[] = $metrics_data;

            if ($pond->fish) {

                $start = $this->start_date->copy();
                $end = $this->end_date->copy();
                $weeks = [];

                while ($start->lessThan($end)) {
                    $start->addDays(7);
                    $weeks[] = $start->copy();
                }

                foreach ($weeks as $week) {
                    $production = fake()->randomFloat(2, 100, 1000);
                    $yield = $production / $pond->size;
                    $survival_rate = fake()->randomFloat(2, 90, 100);
                    $average_weight = fake()->randomFloat(2, 0.5, 2);
                    $average_growth = fake()->randomFloat(2, 0.5, 2);
                    $dissolved_oxygen = fake()->randomFloat(2, 0.5, 2);
                    $water_level = fake()->randomFloat(2, 0.5, 2);
                    $water_temperature = fake()->randomFloat(2, 0.5, 2);
                    $ph = fake()->randomFloat(2, 0.5, 2);
                    $turbidity = fake()->randomFloat(2, 0.5, 2);
                    $ammonia = fake()->randomFloat(2, 0.5, 2);
                    $nitrate = fake()->randomFloat(2, 0.5, 2);

                    $weekly_report_data = [
                        'date' => $week->endOfWeek()->toDateString(),
                        'production' => $production,
                        'yield' => $yield,
                        'survival_rate' => $survival_rate,
                        'average_weight' => $average_weight,
                        'average_growth' => $average_growth,
                        'dissolved_oxygen' => $dissolved_oxygen,
                        'water_level' => $water_level,
                        'water_temperature' => $water_temperature,
                        'ph' => $ph,
                        'turbidity' => $turbidity,
                        'ammonia' => $ammonia,
                        'nitrate' => $nitrate,
                        'farm_id' => $farm->id,
                        'pond_id' => $pond->id,
                    ];

                    $pond_weekly_report_rows[] = $weekly_report_data;
                }
            }
        }

        PondMetrics::query()->insert($pond_metrics_rows);
        PondWeeklyReport::query()->insert($pond_weekly_report_rows);
    }

    private function seedAnimalExpense(): void
    {
        \Log::debug('Seeding Animal Expense');
        $rows = [];
        $animals = Animal::query()->where('type', '=', 'Cow')->get();
        foreach ($animals as $animal) {
            foreach ($this->period as $date) {
                foreach (Enums::$AnimalExpenseType as $type) {
                    if (fake()->boolean(30)) continue;
                    $data = [
                        'type' => $type,
                        'date' => $date->toDateString(),
                        'day' => $date->day,
                        'month' => $date->month,
                        'year' => $date->year,
                        'amount' => fake()->numberBetween(20, 250),
                        'animal_id' => $animal->id,
                        'farm_id' => $animal->farm_id,
                    ];
                    $rows[] = $data;

                    if (count($rows) > $this->batch_insert_limit) {
                        AnimalExpense::query()->insert($rows);
                        $rows = [];
                    }
                }
            }
        }

        AnimalExpense::query()->insert($rows);
    }

    private function seedFarmingExpense(): void
    {
        \Log::debug('Seeding Farming Expense');
        $rows = [];
        $fields = Field::query()->where('status', '=', false)->get();
        foreach ($fields as $field) {
            foreach ($this->period as $date) {
                foreach (Enums::$FarmExpenseType as $type) {
                    if (fake()->boolean(30)) continue;
                    $data = [
                        'type' => $type,
                        'date' => $date->toDateString(),
                        'amount' => fake()->numberBetween(20, 250),
                        'field_id' => $field->id,
                        'farm_id' => $field->farm_id,
                    ];
                    $rows[] = $data;

                    if (count($rows) > $this->batch_insert_limit) {
                        FarmingExpenses::query()->insert($rows);
                        $rows = [];
                    }
                }
            }
        }

        FarmingExpenses::query()->insert($rows);
    }

    private function seedFishExpense(): void
    {
        \Log::debug('Seeding Fish Expense');
        $rows = [];
        $ponds = Pond::query()->whereNotNull('fish')->get();
        foreach ($ponds as $pond) {
            foreach ($this->period as $date) {
                foreach (Enums::$FishExpenseType as $type) {
                    if (fake()->boolean(30)) continue;
                    $data = [
                        'type' => $type,
                        'date' => $date->toDateString(),
                        'amount' => fake()->numberBetween(20, 250),
                        'pond_id' => $pond->id,
                        'farm_id' => $pond->farm_id,
                    ];
                    $rows[] = $data;

                    if (count($rows) > $this->batch_insert_limit) {
                        FishExpenses::query()->insert($rows);
                        $rows = [];
                    }
                }
            }
        }

        FishExpenses::query()->insert($rows);
    }

    private function seedStorageExpense(): void
    {
        \Log::debug('Seeding Storage Expense');
        $rows = [];
        $storages = Storage::all();
        foreach ($storages as $storage) {
            foreach ($this->period as $date) {
                foreach (Enums::$StorageExpenseType as $type) {
                    if (fake()->boolean(30)) continue;

                    $data = [
                        'type' => $type,
                        'date' => $date->toDateString(),
                        'amount' => fake()->numberBetween(20, 250),
                        'storage_id' => $storage->id,
                        'farm_id' => $storage->farm_id,
                    ];
                    $rows[] = $data;

                    if (count($rows) > $this->batch_insert_limit) {
                        StorageExpenses::query()->insert($rows);
                        $rows = [];
                    }
                }
            }
        }

        StorageExpenses::query()->insert($rows);
    }

    private function seedOtherExpense(): void
    {
        \Log::debug('Seeding Other Expense');
        $rows = [];
        $farms = Farm::all();

        $period = $this->start_date->monthsUntil($this->end_date);

        foreach ($farms as $farm) {
            foreach ($period as $month) {
                foreach (array_keys(Enums::$MonthlyExpenseType) as $type) {
//                    if (fake()->boolean(30)) continue;
                    $data = [
                        'type' => $type,
                        'date' => $month->toDateString(),
                        'amount' => Enums::$MonthlyExpenseType[$type],
                        'farm_id' => $farm->id,
                    ];
                    $rows[] = $data;

                    if (count($rows) > $this->batch_insert_limit) {
                        OtherExpenses::query()->insert($rows);
                        $rows = [];
                    }
                }
            }
        }

        OtherExpenses::query()->insert($rows);
    }
}
