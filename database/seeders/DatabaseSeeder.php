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
use App\Models\Field;
use App\Models\Inventory;
use App\Models\Pond;
use App\Models\PurchaseOrder;
use App\Models\Salary;
use App\Models\SalesOrder;
use App\Models\Storage;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Worker;
use App\Utils\Enums;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    private int $months = 3;
    private \Carbon\CarbonPeriod $period;
    private Carbon $end_date;
    private Carbon $start_date;

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

        $this->seedWorkers();
        $this->seedStorage();
        $this->seedAnimals();
        $this->seedAnimalProduction();
//        $this->seedAnimalExpense();
//        $this->seedSuppliers();
        $this->seedPurchaseOrders();
        $this->seedSalaries();
//        $this->seedFields();
        $this->seedCropProjects();
        $this->seedInventory();
        $this->seedPonds();
    }

    private function seedPurchaseOrders(): void
    {
        \Log::debug('Seeding Purchase Orders');
        $suppliers = Supplier::all();
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

                $purchaseOrder = new PurchaseOrder();
                $purchaseOrder->fill($data);
                $purchaseOrder->save();
            }

            //  Update lead time
            $avgLeadTime = PurchaseOrder::query()
                ->select(\DB::raw('AVG(TIMESTAMPDIFF(DAY, order_date, actual_delivery_date)) as avg_lead_time'))
                ->whereNotNull('actual_delivery_date')
                ->where('supplier_id', '=', $supplier->id)
                ->get()
                ->toArray();
            Supplier::query()->where('id', '=', $supplier->id)->update(['lead_time' => $avgLeadTime[0]['avg_lead_time']]);
        }
    }

    private function seedWorkers(): void
    {
        \Log::debug('Seeding Workers');
        Worker::all()->each(function ($worker) {
            foreach ($this->period as $date) {
                $data = [
                    'date' => $date->format('Y-m-d'),
                    'worker_id' => $worker->id,
                ];

                if (fake()->numberBetween(0, 100) > 5) {
                    $data['time_in'] = fake()->dateTimeBetween('06:00:00', '09:00:00')->format('H:i:s');
                    $data['time_out'] = fake()->dateTimeBetween('18:00:00', '20:00:00')->format('H:i:s');
                } else {
                    $data['time_in'] = null;
                    $data['time_out'] = null;
                    $data['leave_reason'] = fake()->sentence();
                }

                $at = new Attendance();
                $at->fill($data);
                $at->save();
            }
        });
    }

    private function seedStorage(): void
    {
        \Log::debug('Seeding Storage');
        Farm::all()->each(function ($farm) {
            for ($i = 0; $i < 10; $i++) {
                $data = [
                    'name' => fake()->buildingNumber(),
                    'type' => fake()->randomElement(Enums::$StorageType),
                    'capacity' => fake()->numberBetween(10, 100),
                    'current_capacity' => 0,
                    'unit' => 'unit',
                    'farm_id' => $farm->id,
                ];

                $storage = new Storage();
                $storage->fill($data);
                $storage->save();
            }
        }
        );

    }

    private function seedAnimals(): void
    {
        \Log::debug('Seeding Animals');
        Storage::query()->where('type', '=', 'Barn')->get()->each(function ($storage) {
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
                $animal = new Animal();
                $animal->fill($data);
                $animal->save();
            }
        });
    }

    private function seedAnimalProduction(): void
    {
        \Log::debug('Seeding Animal Production');
        Animal::query()->where('type', '=', 'Cow')->each(
            function ($animal) {
                foreach ($this->period as $date) {
                    $data = [
                        'type' => 'Milk',
                        'date' => $date->format('Y-m-d'),
                        'quantity' => fake()->numberBetween(1, 10),
                        'unit' => 'litre',
                        'animal_id' => $animal->id,
                        'farm_id' => $animal->farm_id,
                    ];
                    $animalProduction = new AnimalProduction();
                    $animalProduction->fill($data);
                    $animalProduction->save();
                }
            }
        );
    }

    private function seedAnimalExpense(): void
    {
        \Log::debug('Seeding Animal Expense');
        Animal::query()->where('type', '=', 'Cow')->each(
            function ($animal) {
                foreach ($this->period as $date) {
                    foreach (Enums::$AnimalExpenseType as $type) {
                        $data = [
                            'type' => $type,
                            'day' => $date->day,
                            'month' => $date->month,
                            'year' => $date->year,
                            'amount' => fake()->numberBetween(20, 250),
                            'animal_id' => $animal->id,
                            'farm_id' => $animal->farm_id,
                        ];
                        AnimalExpense::query()->create($data);
                    }
                }
            }
        );
    }

    private function seedSalaries(): void
    {
        \Log::debug('Seeding Salaries');
        $now = Carbon::now();

        Worker::all()->each(function ($worker) use ($now) {
            for ($i = 0; $i < $this->months; $i++) {
                $month = $now->copy()->subMonths($i);

                $res = $this->salaryController->getSalaryReportIndividual($worker->id)
                    ->whereMonth('attendances.date', '=', $month->month)
                    ->whereYear('attendances.date', '=', $month->year)
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

                $salary = new Salary();
                $salary->fill([
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
                ]);
                $salary->save();
            }
        });

    }

    private function seedCropProjects(): void
    {
        \Log::debug('Seeding Crop Projects');

        $totalFields = Field::count();

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
            $crop->fill($data);
            $crop->save();
            Field::query()->where('id', '=', $field['id'])->update(['status' => false]);
        }
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
        $orders = PurchaseOrder::query()->whereNotNull('actual_delivery_date')->get();
        $orders->every(function (PurchaseOrder $order) {

            $isOperational = fake()->boolean(85);
            $reasonForFailure = null;
            if (!$isOperational) {
                $reasonForFailure = fake()->sentence();
            }

            [$storageType, $type] = Enums::getStorage($order->type);

            $storage_id = fake()->randomElement(Storage::query()
                ->where('type', '=', $storageType)
                ->where('farm_id', '=', $order->farm_id)
                ->get()->random(1)->first()->pluck('id'));


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

            $inventory = new Inventory();
            $inventory->fill($data);
            $inventory->save();
        });
    }

    private function seedPonds(): void
    {
        \Log::debug('Seeding Ponds');

        $metrics_path = storage_path('pond.json');
        $metrics = json_decode(file_get_contents($metrics_path), true);
        $len = count($metrics["ph"]);

        $farms = Farm::all();

        foreach($farms as $farm) {
            foreach (range(0, 10) as $_) {
                $data = [
                    'name' => fake()->streetName(),
                    'pond_type' => Enums::$PondType[0],
                    'water_type' => fake()->randomElement(Enums::$WaterType),
                    'fish' => fake()->boolean(70) ? fake()->randomElement(Enums::$FishName) : null,
                    'size' => fake()->randomFloat(2, 50, 100),
                    'farm_id' => $farm->id,
                ];

                $pond = new Pond();
                $pond->fill($data);
                $pond->save();

                $r = rand(0, $len);
                $metrics_data = [
                    'water_temperature' => $metrics["temperature"][$r],
                    'ph' => $metrics["ph"][$r],
                    'turbidity' => $metrics["turbidity"][$r],
                    'farm_id' => $farm->id,
                    'pond_id' => $pond->id,
                ];

                $pond->pondMetrics()->create($metrics_data);

                if ($pond->fish) {

                    $start = $this->start_date->copy();
                    $end = $this->end_date->copy();
                    $weeks = [];

                    while($start->lessThan($end)) {
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

                        $pond->pondWeeklyReports()->create($weekly_report_data);
                    }
                }
            }
        }
    }

    private function seedSuppliers(): void
    {
        \Log::debug('Seeding Suppliers');
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
                $supplier = new Supplier();
                $supplier->fill($data);
                $supplier->save();
            }
        }
    }

    private function seedFields(): void
    {
        \Log::debug('Seeding Fields');
        Farm::all()->each(
            function ($farm) {
                for ($i = 0; $i < 10; $i++) {
                    $data = [
                        'address' => fake()->address(),
                        'area' => fake()->numberBetween(10, 100),
                        'name' => fake()->firstNameFemale(),
                        'soil_type' => fake()->randomElement(Enums::$SoilType),
                        'status' => true,
                        'farm_id' => $farm->id,
                    ];
                    Field::query()->create($data);
                }
            }
        );
    }
}
