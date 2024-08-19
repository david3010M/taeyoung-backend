<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    public function definition(): array
    {
        static $correlativo = 0;

        $correlativo++;
        $correlativoFormatted = str_pad($correlativo, 8, '0', STR_PAD_LEFT);

        return [
            'number' => $correlativoFormatted,
            'detail' => $this->faker->text(100),
            'date' => $this->faker->dateTimeThisYear(),
            'currencyType' => 'PEN',
            'client_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
