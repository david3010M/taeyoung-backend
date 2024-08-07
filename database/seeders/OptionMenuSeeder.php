<?php

namespace Database\Seeders;

use App\Models\OptionMenu;
use Illuminate\Database\Seeder;

class OptionMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['id' => 1, 'name' => 'Venta de Repuestos', 'route' => 'ventaRespuestos', 'order' => 1, 'icon' => 'fas fa-cogs', 'groupmenu_id' => 1],
            ['id' => 2, 'name' => 'Venta de Maquinaría', 'route' => 'ventaMaquinaria', 'order' => 2, 'icon' => 'fas fa-cogs', 'groupmenu_id' => 1],
            ['id' => 3, 'name' => 'Compra de Repuestos', 'route' => 'compraRespuestos', 'order' => 1, 'icon' => 'fas fa-shopping-cart', 'groupmenu_id' => 2],
            ['id' => 4, 'name' => 'Cotización de Maquinaria', 'route' => 'cotizacionMaquinaria', 'order' => 2, 'icon' => 'fas fa-shopping-cart', 'groupmenu_id' => 2],
            ['id' => 5, 'name' => 'Compra de Maquinaría', 'route' => 'compraMaquinaria', 'order' => 3, 'icon' => 'fas fa-shopping-cart', 'groupmenu_id' => 2],
            ['id' => 6, 'name' => 'Repuestos', 'route' => 'repuestos', 'order' => 1, 'icon' => 'fas fa-boxes', 'groupmenu_id' => 3],
            ['id' => 7, 'name' => 'Kardex de Repuesto', 'route' => 'kardexRepuesto', 'order' => 1, 'icon' => 'fas fa-boxes', 'groupmenu_id' => 4],
            ['id' => 8, 'name' => 'Reporte de Ingresos', 'route' => 'report_ingresos', 'order' => 2, 'icon' => 'fas fa-chart-line', 'groupmenu_id' => 4],
            ['id' => 9, 'name' => 'Reporte de Egresos', 'route' => 'report_egresos', 'order' => 3, 'icon' => 'fas fa-chart-line', 'groupmenu_id' => 4],
            ['id' => 10, 'name' => 'Balance', 'route' => 'balance', 'order' => 4, 'icon' => 'fas fa-chart-line', 'groupmenu_id' => 4],
            ['id' => 11, 'name' => 'Dashborad', 'route' => 'dashboard', 'order' => 1, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['id' => 12, 'name' => 'Ingresos', 'route' => 'ingresos', 'order' => 2, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['id' => 13, 'name' => 'Egresos', 'route' => 'egresos', 'order' => 3, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['id' => 14, 'name' => 'Usuarios', 'route' => 'usuarios', 'order' => 4, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['id' => 15, 'name' => 'Paises', 'route' => 'paises', 'order' => 5, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['id' => 16, 'name' => 'Tipo de Cambio', 'route' => 'tipoCambio', 'order' => 6, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5],
            ['id' => 17, 'name' => 'Proveedor', 'route' => 'proveedor', 'order' => 7, 'icon' => 'fas fa-cog', 'groupmenu_id' => 5]
        ];

        foreach ($array as $item) {
            OptionMenu::create($item);
        }
    }
}
