<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DetailSparePartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quantity' => $this->faker->randomNumber(2),
            'movementType' => $this->faker->randomElement(['purchase', 'sale']),
            'purchasePrice' => $this->faker->randomFloat(2, 0, 1000),
            'salePrice' => $this->faker->randomFloat(2, 0, 1000),
            'order_id' => $this->faker->randomNumber(2),
            'quotation_id' => $this->faker->randomNumber(2),
            'spare_part_id' => $this->faker->randomNumber(1),
        ];
    }
}
