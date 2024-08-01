<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomCurrency1 = $this->faker->randomElement(['USD', 'PEN']);
        $randomCurrency2 = $randomCurrency1 === 'USD' ? 'PEN' : 'USD';

        return [
            'currencyFrom' => $randomCurrency1,
            'currencyTo' => $randomCurrency2,
            'rate' => $this->faker->randomFloat(2, 3.5, 4),
            'date' => $this->faker->date(),
        ];
    }
}
