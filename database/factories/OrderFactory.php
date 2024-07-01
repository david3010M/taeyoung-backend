<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $minSupplierId = Person::where('type', 'supplier')->min('id');
        $maxSupplierId = Person::where('type', 'supplier')->max('id');

        return [
            'date' => $this->faker->date(),
            'number' => $this->faker->unique()->numberBetween(1000, 9999),
            'documentType' => $this->faker->randomElement(['invoice', 'receipt']),
            'detail' => $this->faker->sentence(15),
            'quantity' => $this->faker->numberBetween(1, 100),
            'totalIncome' => $this->faker->randomFloat(2, 1000, 100000),
            'totalExpense' => $this->faker->randomFloat(2, 500, 50000),
            'currency' => $this->faker->randomElement(['USD', 'EUR']),
            'typePayment' => $this->faker->randomElement(['cash', 'credit']),
            'comment' => $this->faker->sentence(10),
            'supplier_id' => $this->faker->numberBetween($minSupplierId, $maxSupplierId),
        ];
    }

    public function machineryPurchase()
    {
        return $this->state([
            'type' => 'machinery_purchase',
        ]);
    }

    public function machinerySale()
    {
        return $this->state([
            'type' => 'machinery_sale',
        ]);
    }

    public function sparePartPurchase()
    {
        return $this->state([
            'type' => 'spare_part_purchase',
        ]);
    }

    public function sparePartSale()
    {
        return $this->state([
            'type' => 'spare_part_sale',
        ]);
    }
}
