<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DetailMachineryFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $price = $this->faker->randomFloat(2, 1, 1000);
        $purchasePrice = $price;
        $salePrice = $price * 1.2;
        return [
            'quantity' => $quantity,
            'description' => $this->faker->sentence,
            'purchasePrice' => $purchasePrice,
            'salePrice' => $salePrice,
            'purchaseValue' => $purchasePrice * $quantity,
            'saleValue' => $salePrice * $quantity,
        ];
    }

    public function purchase()
    {
        return $this->state([
            'movementType' => 'purchase',
        ]);
    }

    public function sale()
    {
        return $this->state([
            'movementType' => 'sale',
        ]);
    }
}
