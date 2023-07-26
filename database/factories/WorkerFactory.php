<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Utils\Enums;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Worker>
 */
class WorkerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'designation' => $this->faker->randomElement(Enums::$WorkerDesignations),
            'phone_number' => $this->faker->phoneNumber,
            'start_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'end_date' => null,
            'salary' => $this->faker->randomFloat(2, 1000, 10000),
            'bonus' => $this->faker->randomFloat(2, 100, 1000),
            'over_time_rate' => $this->faker->randomFloat(2, 500, 2000),
            'expected_hours' => $this->faker->boolean ? 7 : 8,
        ];
    }
}
