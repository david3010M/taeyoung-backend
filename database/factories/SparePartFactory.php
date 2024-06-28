<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SparePart>
 */
class SparePartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numberBetween(100000, 999999),
            'name' => $this->faker->word(),
            'purchasePrice' => $this->faker->randomFloat(2, 1, 1000),
            'salePrice' => $this->faker->randomFloat(2, 1, 1000),
            'stock' => $this->faker->numberBetween(1, 100),
        ];
    }
}
