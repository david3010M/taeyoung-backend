<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        $saleRate = $this->faker->randomFloat(2, 3.8, 4);
        $buyRate = $saleRate - $this->faker->randomFloat(2, 0.01, 0.1);

        return [
            'buyRate' => $buyRate,
            'saleRate' => $saleRate,
            'date' => $this->faker->date(),
        ];
    }
}
