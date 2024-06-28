<?php

namespace Database\Factories;

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
        return [
            'type' => 'client',
            'ruc' => $this->faker->unique()->numberBetween(10000000000, 99999999999),
            'businessName' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->numberBetween(900000000, 999999999),
            'representativeDni' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'representativeNames' => $this->faker->name(),
            'country_id' => $this->faker->numberBetween(1, 4),
        ];
    }
}
