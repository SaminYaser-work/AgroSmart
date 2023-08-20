<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Utils\Enums;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $order_date = Carbon::now()->subDays(rand(1, 30));
        $expected_delivery_date = Carbon::parse($order_date)->addDays(rand(1, 30));
        $actual_delivery_date = null; // Not delivered yet

        if ($this->faker->boolean(30)) {
            $actual_delivery_date = Carbon::parse($expected_delivery_date)->subDays(); // On time
        } elseif ($this->faker->boolean(30)) {
            $actual_delivery_date = Carbon::parse($expected_delivery_date)->addDays(rand(1, 7)); // late
        }

        $quantity = $this->faker->numberBetween(1, 100);
        $unit_price = $this->faker->randomFloat(2, 100, 2000);
        $amount = $quantity * $unit_price;

        $type = $this->faker->randomElement(Enums::$ItemType);
        $unit = $type === 'Dairy' ? 'litre' : 'kg';

        return [
            'name' => $this->faker->randomElement(Enums::$SaleItem),
            'type' => $type,
            'order_date' => $order_date,
            'expected_delivery_date' => $expected_delivery_date,
            'actual_delivery_date' => $actual_delivery_date,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'amount' => $amount,
            'unit' => $unit,
            'farm_id' => $this->faker->randomElement(Farm::all()->pluck('id')->toArray())
        ];
    }
}
