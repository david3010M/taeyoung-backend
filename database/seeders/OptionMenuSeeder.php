<?php

namespace Database\Seeders;

use App\Models\OptionMenu;
use Illuminate\Database\Seeder;

class OptionMenuSeeder extends Seeder
{
    public function run(): void
    {
        $array = [
            ['id' => 1, 'name' => 'Cotización', 'route' => 'cotizacion', 'order' => 1, 'icon' => 'fas fa-shopping-cart', 'groupmenu_id' => 1],
            ['id' => 2, 'name' => 'Venta', 'route' => 'venta', 'order' => 2, 'icon' => 'fas fa-cogs', 'groupmenu_id' => 1],
            ['id' => 3, 'name' => 'Compra', 'route' => 'compra', 'order' => 1, 'icon' => 'fas fa-shopping-cart', 'groupmenu_id' => 1],
            ['id' => 4, 'name' => 'Cuentas por Cobrar', 'route' => 'cuentasCobrar', 'order' => 1, 'icon' => 'fas fa-boxes', 'groupmenu_id' => 2],
            ['id' => 5, 'name' => 'Cuentas por Pagar', 'route' => 'cuentasPagar', 'order' => 2, 'icon' => 'fas fa-boxes', 'groupmenu_id' => 2],
            ['id' => 6, 'name' => 'Repuestos', 'route' => 'repuestos', 'order' => 1, 'icon' => 'fas fa-boxes', 'groupmenu_id' => 3],
            ['id' => 7, 'name' => 'Usuarios', 'route' => 'usuarios', 'order' => 1, 'icon' => 'fas fa-cog', 'groupmenu_id' => 4],
            ['id' => 8, 'name' => 'Paises', 'route' => 'paises', 'order' => 2, 'icon' => 'fas fa-cog', 'groupmenu_id' => 4],
            ['id' => 9, 'name' => 'Tipo de Cambio', 'route' => 'tipoCambio', 'order' => 3, 'icon' => 'fas fa-cog', 'groupmenu_id' => 4],
            ['id' => 10, 'name' => 'Proveedor', 'route' => 'proveedor', 'order' => 4, 'icon' => 'fas fa-cog', 'groupmenu_id' => 4],
            ['id' => 11, 'name' => 'Cliente', 'route' => 'cliente', 'order' => 5, 'icon' => 'fas fa-cog', 'groupmenu_id' => 4],
            ['id' => 12, 'name' => 'Conceptos de Pago', 'route' => 'conceptosDePago', 'order' => 6, 'icon' => 'fas fa-cog', 'groupmenu_id' => 4],
        ];

        foreach ($array as $item) {
            OptionMenu::create($item);
        }
    }
}
