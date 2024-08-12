<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DetailMachineryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence,
        ];
    }

    public function purchase()
    {
        $purchasePrice = $this->faker->randomFloat(2, 1, 1000);
        $quantity = $this->faker->numberBetween(1, 10);
        return $this->state([
            'quantity' => $quantity,
            'movementType' => 'purchase',
            'purchasePrice' => $purchasePrice,
            'purchaseValue' => $purchasePrice * $quantity,
        ]);
    }

    public function sale()
    {
        $salePrice = $this->faker->randomFloat(2, 1, 1000);
        $quantity = $this->faker->numberBetween(1, 10);
        return $this->state([
            'quantity' => $quantity,
            'movementType' => 'sale',
            'salePrice' => $salePrice,
            'saleValue' => $salePrice * $quantity,
        ]);
    }
}
