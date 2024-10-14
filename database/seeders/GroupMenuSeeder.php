<?php

namespace Database\Seeders;

use App\Models\GroupMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupMenuSeeder extends Seeder
{
    public function run(): void
    {
        $array = [
            ['name' => 'Transacciones', 'icon' => 'ShoppingBag', 'order' => 1],
            ['name' => 'Cuentas', 'icon' => 'ShoppingCart', 'order' => 2],
            ['name' => 'Inventario', 'icon' => 'Package', 'order' => 3],
            ['name' => 'Reportes', 'icon' => 'FileText', 'order' => 4],
            ['name' => 'Configuración', 'icon' => 'Settings', 'order' => 5]
        ];

        foreach ($array as $item) {
            GroupMenu::create($item);
        }

    }
}
