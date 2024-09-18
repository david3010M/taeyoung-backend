<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Person;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        static $correlativo = 0;
        $correlativo++;
        $correlativoFormatted = str_pad($correlativo, 8, '0', STR_PAD_LEFT);

        $randomMethodPayNumber = $this->faker->numberBetween(1, 2);

        return [
            'number' => $correlativoFormatted,
            'date' => $this->faker->date(),
            'detail' => $this->faker->sentence(15),

            'documentType' => $this->faker->randomElement(['BOLETA', 'FACTURA']),
            'paymentType' => $randomMethodPayNumber == 1 ? 'CONTADO' : 'CREDITO',
            'status' => $randomMethodPayNumber == 1 ? 'PAGADO' : 'PENDIENTE',
            'currencyType' => $this->faker->randomElement(['USD', 'PEN']),

            'totalMachinery' => $this->faker->randomFloat(2, 1000, 100000),
            'totalSpareParts' => $this->faker->randomFloat(2, 500, 50000),

//            'subtotal' => $this->faker->randomFloat(2, 1000, 100000),
//            'igv' => $this->faker->randomFloat(2, 100, 10000),
//            'discount' => $this->faker->randomFloat(2, 100, 10000),
//            'total' => $this->faker->randomFloat(2, 1000, 100000),

//            'totalIncome' => $this->faker->randomFloat(2, 1000, 100000),
//            'totalExpense' => $this->faker->randomFloat(2, 1000, 100000),

            'comment' => $this->faker->sentence(10),
        ];
    }

    public function purchase()
    {
        return $this->state(function (array $attributes) {
            $suppliers = Person::where('type', 'supplier')->get();
            return [
                'type' => 'purchase',
                'supplier_id' => $suppliers->random()->id,
            ];
        });
    }

    public function sale()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'sale',
            ];
        });
    }
}
