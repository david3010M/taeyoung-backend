<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DetailMachineryFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        return [
            'description' => $this->faker->sentence,
            'quantity' => $quantity,
        ];
    }

    public function purchase()
    {
        $purchasePrice = $this->faker->randomFloat(2, 1, 1000);
        return $this->state([
            'movementType' => 'purchase',
            'purchasePrice' => $purchasePrice,
        ]);
    }

    public function sale()
    {
        $salePrice = $this->faker->randomFloat(2, 1, 1000);
        return $this->state([
            'movementType' => 'sale',
            'salePrice' => $salePrice,
        ]);
    }
}
