<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class SparePartFactory extends Factory
{
    public function definition(): array
    {
        static $correlativo = 0;
        $correlativo++;
        $correlativoFormatted = str_pad($correlativo, 8, '0', STR_PAD_LEFT);
        $units = Unit::all();
        $price = $this->faker->randomFloat(2, 1, 1000);
        return [
            'code' => $correlativoFormatted,
            'name' => $this->faker->word(),
            'purchasePrice' => $price,
            'salePrice' => $price * 1.2,
            'stock' => $this->faker->numberBetween(1, 100),
            'unit_id' => $units->random()->id,
        ];
    }
}
