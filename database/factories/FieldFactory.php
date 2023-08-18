<?php

namespace Database\Factories;

use App\Utils\Enums;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Field>
 */
class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address' => $this->faker->address,
            'area' => $this->faker->randomFloat(2, 0, 100),
            'name' => $this->faker->company,
            'soil_type' => $this->faker->randomElement(Enums::$SoilType),
            'status' => true,
            'farm_id' => \App\Models\Farm::factory(),
        ];
    }
}
