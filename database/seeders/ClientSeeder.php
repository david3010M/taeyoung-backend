<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Person::factory()->count(10)->create([
            'type' => 'client',
        ]);
    }
}
