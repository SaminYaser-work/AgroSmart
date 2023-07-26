<?php

namespace Database\Factories;

use App\Utils\Enums;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Storage>
 */
class StorageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'type' => $this->faker->randomElement(Enums::$StorageType),
            'capacity' => $this->faker->randomFloat(2, 0, 100000),
            'current_capacity' => $this->faker->randomFloat(2, 0, 100000),
            'unit' => $this->faker->randomElement(Enums::$Units),
        ];
    }
}
