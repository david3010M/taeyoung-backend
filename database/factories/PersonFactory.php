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
        $names = $this->faker->name;
        $fatherSurname = $this->faker->lastName;
        $motherSurname = $this->faker->lastName;
        $businessName = $this->faker->company;
        $filterName = $typeDocument === 'DNI' ? "$names $fatherSurname $motherSurname" : $businessName;
        return [
            'type' => 'client',
            'typeDocument' => $typeDocument,
            'dni' => $typeDocument === 'DNI' ? $this->faker->unique()->numberBetween(10000000, 99999999) : null,
            'ruc' => $typeDocument === 'RUC' ? $this->faker->unique()->numberBetween(10000000000, 99999999999) : null,
            'filterName' => $filterName,
            'businessName' => $typeDocument === 'RUC' ? $businessName : null,
            'names' => $typeDocument === 'DNI' ? $names : null,
            'fatherSurname' => $typeDocument === 'DNI' ? $fatherSurname : null,
            'motherSurname' => $typeDocument === 'DNI' ? $motherSurname : null,
            'address' => $this->faker->address,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->numberBetween(100000000, 999999999),
            'representativeDni' => $typeDocument === 'RUC' ? $this->faker->unique()->numberBetween(10000000, 99999999) : null,
            'representativeNames' => $typeDocument === 'RUC' ? $this->faker->name : null,
            'country_id' => $countries->random()->id,
        ];
    }

    public function supplier(): self
    {
        return $this->state(function (array $attributes) {
            $businessName = $this->faker->unique()->company; // Genera un nuevo businessName único cada vez
            return [
                'type' => 'supplier',
                'typeDocument' => 'RUC',
                'dni' => null,
                'ruc' => $this->faker->unique()->numberBetween(10000000000, 99999999999),
                'filterName' => $businessName,
                'businessName' => $businessName,
                'names' => null,
                'fatherSurname' => null,
                'motherSurname' => null,
            ];
        });
    }
}
