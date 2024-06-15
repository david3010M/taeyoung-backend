<?php

namespace Database\Seeders;

use App\Models\TypeUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['name' => 'Administrador'],
            ['name' => 'Vendedor'],
            ['name' => 'Cliente'],
        ];

        foreach ($array as $item) {
            TypeUser::create($item);
        }
    }
}
