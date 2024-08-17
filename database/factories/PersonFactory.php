<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $countries = Country::all();
        $typeDocument = $this->faker->randomElement(['DNI', 'RUC']);
        return [
            'type' => 'client',
            'typeDocument' => $typeDocument,
            'dni' => $typeDocument === 'DNI' ? $this->faker->unique()->numberBetween(10000000, 99999999) : null,
            'ruc' => $typeDocument === 'RUC' ? $this->faker->unique()->numberBetween(10000000000, 99999999999) : null,
            'businessName' => $typeDocument === 'RUC' ? $this->faker->company : null,
            'names' => $typeDocument === 'DNI' ? $this->faker->firstName : null,
            'fatherSurname' => $typeDocument === 'DNI' ? $this->faker->lastName : null,
            'motherSurname' => $typeDocument === 'DNI' ? $this->faker->lastName : null,
            'address' => $this->faker->address,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->numberBetween(100000000, 999999999),
            'representativeDni' => $typeDocument === 'RUC' ? $this->faker->unique()->numberBetween(10000000, 99999999) : null,
            'representativeNames' => $typeDocument === 'RUC' ? $this->faker->name : null,
            'country_id' => $countries->random()->id,
        ];
    }
}
