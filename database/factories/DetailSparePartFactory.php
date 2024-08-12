<?php

namespace Database\Factories;

use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailSparePartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quantity' => $this->faker->numberBetween(1, 10),
            'movementType' => $this->faker->randomElement(['purchase', 'sale']),
        ];
    }

    public function purchase()
    {
        $spareParts = SparePart::all();
        $sparePart = $this->faker->randomElement($spareParts);
        $quantity = $this->faker->numberBetween(1, 10);
        $purchasePrice = $sparePart->purchasePrice;
        return $this->state([
            'movementType' => 'purchase',
            'quantity' => $quantity,
            'purchasePrice' => $purchasePrice,
            'purchaseValue' => $quantity * $purchasePrice,
            'spare_part_id' => $sparePart->id,
        ]);
    }

    public function sale()
    {
        $spareParts = SparePart::all();
        $sparePart = $this->faker->randomElement($spareParts);
        $quantity = $this->faker->numberBetween(1, 10);
        $salePrice = $sparePart->salePrice;
        return $this->state([
            'movementType' => 'sale',
            'quantity' => $quantity,
            'salePrice' => $salePrice,
            'saleValue' => $quantity * $salePrice,
            'spare_part_id' => $sparePart->id,
        ]);
    }
}
