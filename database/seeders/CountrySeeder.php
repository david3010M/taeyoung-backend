<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['name' => 'Corea'], ['name' => 'Turquia'], ['name' => 'India'], ['name' => 'Colombia']
        ];

        foreach ($array as $item) {
            Country::create($item);
        }
    }
}
