<?php

namespace Database\Factories;

use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailSparePartFactory extends Factory
{
    public function definition(): array
    {
        $spareParts = SparePart::all();
        $sparePart = $this->faker->randomElement($spareParts);
        $quantity = $this->faker->numberBetween(1, 10);
        $purchasePrice = $sparePart->purchasePrice;
        $salePrice = $sparePart->salePrice;
        return [
            'quantity' => $quantity,
            'purchasePrice' => $purchasePrice,
            'salePrice' => $salePrice,
            'purchaseValue' => $quantity * $purchasePrice,
            'saleValue' => $quantity * $salePrice,
            'spare_part_id' => $sparePart->id,
        ];
    }

    public function quotation()
    {
        return $this->state([
            'movementType' => 'quotation',
        ]);
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
