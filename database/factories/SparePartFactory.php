<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class SparePartFactory extends Factory
{
    public function definition(): array
    {
        $units = Unit::all();
        return [
            'code' => $this->faker->unique()->numberBetween(100000, 999999),
            'name' => $this->faker->word(),
            'purchasePrice' => $this->faker->randomFloat(2, 1, 1000),
            'salePrice' => $this->faker->randomFloat(2, 1, 1000),
            'stock' => $this->faker->numberBetween(1, 100),
            'unit_id' => $units->random()->id,
        ];
    }
}
