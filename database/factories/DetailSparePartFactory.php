<?php

namespace Database\Factories;

use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailSparePartFactory extends Factory
{
    public function definition(): array
    {
        $spareParts = SparePart::all();
        return [
            'quantity' => $this->faker->randomNumber(1),
            'movementType' => $this->faker->randomElement(['purchase', 'sale']),
            'purchasePrice' => $this->faker->randomFloat(2, 0, 1000),
            'salePrice' => $this->faker->randomFloat(2, 0, 1000),
            'spare_part_id' => $this->faker->randomElement($spareParts)->id,
        ];
    }
}
