<?php

namespace Database\Seeders;

use App\Models\GroupMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['name' => 'Administración', 'icon' => 'fas fa-cogs', 'order' => 1],
            ['name' => 'Ventas', 'icon' => 'fas fa-shopping-cart', 'order' => 2],
            ['name' => 'Inventario', 'icon' => 'fas fa-boxes', 'order' => 3],
            ['name' => 'Reportes', 'icon' => 'fas fa-chart-line', 'order' => 4],
            ['name' => 'Configuración', 'icon' => 'fas fa-cog', 'order' => 5]
        ];

        foreach ($array as $item) {
            GroupMenu::create($item);
        }

    }
}
