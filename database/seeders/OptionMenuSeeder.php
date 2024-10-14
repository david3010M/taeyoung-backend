<?php

namespace Database\Seeders;

use App\Models\OptionMenu;
use Illuminate\Database\Seeder;

class OptionMenuSeeder extends Seeder
{
    public function run(): void
    {
        $array = [
            ['name' => 'Cotización', 'route' => 'cotizacion', 'order' => 1, 'icon' => 'fas fa-shopping-cart', 'groupmenu_id' => 1],
            ['name' => 'Venta', 'route' => 'venta', 'order' => 2, 'icon' => 'fas fa-cogs', 'groupmenu_id' => 1],
            ['name' => 'Compra', 'route' => 'compra', 'order' => 1, 'icon' => 'fas fa-shopping-cart', 'groupmenu_id' => 1],
            ['name' => 'Cuentas por Cobrar', 'route' => 'cuentasCobrar', 'order' => 1, 'icon' => 'fas fa-boxes', 'groupmenu_id' => 2],
            ['name' => 'Cuentas por Pagar', 'route' => 'cuentasPagar', 'order' => 2, 'icon' => 'fas fa-boxes', 'groupmenu_id' => 2],
            ['name' => 'Repuestos', 'route' => 'repuestos', 'order' => 1, 'icon' => 'fas fa-boxes', 'groupmenu_id' => 3],
            ['name' => 'Kardex de Repuesto', 'route' => 'kardexRepuesto', 'order' => 1, 'icon' => 'fas fa-boxes', 'groupmenu_id' => 4],
            ['name' => 'Reporte de Ingresos', 'route' => 'report_ingresos', 'order' => 2, 'icon' => 'fas fa-chart-line', 'groupmenu_id' => 4],
            ['name' => 'Reporte de Egresos', 'route' => 'report_egresos', 'order' => 3, 'icon' => 'fas fa-chart-line', 'groupmenu_id' => 4],
            ['name' => 'Balance', 'route' => 'balance', 'order' => 4, 'icon' => 'fas fa-chart-line', 'groupmenu_id' => 4],
            ['name' => 'Dashborad', 'route' => 'dashboard', 'order' => 1, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['name' => 'Ingresos', 'route' => 'ingresos', 'order' => 2, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['name' => 'Egresos', 'route' => 'egresos', 'order' => 3, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['name' => 'Usuarios', 'route' => 'usuarios', 'order' => 4, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['name' => 'Paises', 'route' => 'paises', 'order' => 5, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['name' => 'Tipo de Cambio', 'route' => 'tipoCambio', 'order' => 6, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['name' => 'Proveedor', 'route' => 'proveedor', 'order' => 7, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['name' => 'Cliente', 'route' => 'cliente', 'order' => 7, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['name' => 'Conceptos de Pago', 'route' => 'conceptosDePago', 'order' => 8, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
        ];

        foreach ($array as $item) {
            OptionMenu::create($item);
        }
    }
}
