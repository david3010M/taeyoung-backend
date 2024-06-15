<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            [
                'type' => 'supplier',
                'ruc' => '12345678912',
                'businessName' => 'Supplier 1',
                'address' => 'Jr. Los Pinos 123',
                'email' => 'supplier1@gmail.com',
                'phone' => '945678123',
                'representativeDni' => '12345678',
                'representativeNames' => 'Juan Perez',
                'country_id' => 1
            ]
        ];
    }
}
