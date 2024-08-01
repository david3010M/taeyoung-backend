<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 1000, 10000);
        $initialPayment = $price / 2;
        $balance = $price - $initialPayment;
        $debtsToPay = $this->faker->randomElement([2, 4, 6]);

        static $correlativo = 0;

        $correlativo++;
        $correlativoFormatted = str_pad($correlativo, 8, '0', STR_PAD_LEFT);

        return [
            'number' => $correlativoFormatted,
            'detail' => $this->faker->text(100),
            'date' => $this->faker->dateTimeThisYear(),
            'currencyType' => 'USD',
            'price' => $price,
            'initialPayment' => $initialPayment,
            'balance' => $balance,
            'debts' => $debtsToPay,
            'exchangeRate' => $this->faker->randomFloat(2, 3.5, 4.5),
            'currency_id' => $this->faker->numberBetween(1, 2),
            'client_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
