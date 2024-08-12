<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'unidad', 'abbreviation' => 'und'],
            ['name' => 'pieza', 'abbreviation' => 'pz'],
            ['name' => 'docena', 'abbreviation' => 'dz'],
            ['name' => 'decena', 'abbreviation' => 'dza'],

        ];
        foreach ($data as $unit) {
            Unit::create($unit);
        }
    }
}
