<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    public function definition(): array
    {
        static $correlativo = 0;

        $correlativo++;
        $correlativoFormatted = str_pad($correlativo, 8, '0', STR_PAD_LEFT);

        $clients = Person::where('type', 'client')->get();

        return [
            'number' => $correlativoFormatted,
            'detail' => $this->faker->text(100),
            'date' => $this->faker->dateTimeThisYear(),
            'currencyType' => 'PEN',
            'client_id' => $clients->random()->id,
        ];
    }
}
