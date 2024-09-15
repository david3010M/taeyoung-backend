<?php

namespace Database\Seeders;

use App\Models\PaymentConcept;
use Illuminate\Database\Seeder;

class PaymentConceptSeeder extends Seeder
{

    public function run(): void
    {
        $array = [
            ['number' => '00000001', 'name' => 'Apertura', 'type' => 'Ingreso'],
            ['number' => '00000002', 'name' => 'Cierre', 'type' => 'Egreso'],

            ['number' => '00000003', 'name' => 'Pago de proveedores', 'type' => 'Egreso'],
            ['number' => '00000004', 'name' => 'Ingreso por servicios adicionales', 'type' => 'Ingreso'],
            ['number' => '00000005', 'name' => 'Retiro de efectivo para gastos menores', 'type' => 'Egreso'],
            ['number' => '00000006', 'name' => 'Pago de salario de empleados', 'type' => 'Egreso'],
            ['number' => '00000007', 'name' => 'Cobro de ventas', 'type' => 'Ingreso']
        ];

        foreach ($array as $item) {
            PaymentConcept::create($item);
        }
    }
}
