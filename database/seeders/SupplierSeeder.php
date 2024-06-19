<?php

namespace Database\Seeders;

use App\Models\Person;
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
                'email' => 'supplier1@gmail.com',
                'phone' => '945678123',
                'representativeDni' => '12345678',
                'representativeNames' => 'Juan Perez',
                'country_id' => 1
            ],
            [
                'type' => 'supplier',
                'ruc' => '98765432101',
                'businessName' => 'Supplier 2',
                'email' => 'supplier2@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '87654321',
                'representativeNames' => 'Maria Perez',
                'country_id' => 2
            ],
            [
                'type' => 'supplier',
                'ruc' => '45678912345',
                'businessName' => 'Supplier 3',
                'email' => 'supplier3@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '45678912',
                'representativeNames' => 'Pedro Perez',
                'country_id' => 3
            ],
            [
                'type' => 'supplier',
                'ruc' => '12345678912',
                'businessName' => 'Supplier 1',
                'email' => 'supplier1@gmail.com',
                'phone' => '945678123',
                'representativeDni' => '12345678',
                'representativeNames' => 'Juan Perez',
                'country_id' => 1
            ],
            [
                'type' => 'supplier',
                'ruc' => '98765432101',
                'businessName' => 'Supplier 2',
                'email' => 'supplier2@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '87654321',
                'representativeNames' => 'Maria Perez',
                'country_id' => 2
            ],
            [
                'type' => 'supplier',
                'ruc' => '45678912345',
                'businessName' => 'Supplier 3',
                'email' => 'supplier3@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '45678912',
                'representativeNames' => 'Pedro Perez',
                'country_id' => 3
            ],
            [
                'type' => 'supplier',
                'ruc' => '12345678912',
                'businessName' => 'Supplier 1',
                'email' => 'supplier1@gmail.com',
                'phone' => '945678123',
                'representativeDni' => '12345678',
                'representativeNames' => 'Juan Perez',
                'country_id' => 1
            ],
            [
                'type' => 'supplier',
                'ruc' => '98765432101',
                'businessName' => 'Supplier 2',
                'email' => 'supplier2@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '87654321',
                'representativeNames' => 'Maria Perez',
                'country_id' => 2
            ],
            [
                'type' => 'supplier',
                'ruc' => '45678912345',
                'businessName' => 'Supplier 3',
                'email' => 'supplier3@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '45678912',
                'representativeNames' => 'Pedro Perez',
                'country_id' => 3
            ],
            [
                'type' => 'supplier',
                'ruc' => '12345678912',
                'businessName' => 'Supplier 1',
                'email' => 'supplier1@gmail.com',
                'phone' => '945678123',
                'representativeDni' => '12345678',
                'representativeNames' => 'Juan Perez',
                'country_id' => 1
            ],
            [
                'type' => 'supplier',
                'ruc' => '98765432101',
                'businessName' => 'Supplier 2',
                'email' => 'supplier2@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '87654321',
                'representativeNames' => 'Maria Perez',
                'country_id' => 2
            ],
            [
                'type' => 'supplier',
                'ruc' => '45678912345',
                'businessName' => 'Supplier 3',
                'email' => 'supplier3@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '45678912',
                'representativeNames' => 'Pedro Perez',
                'country_id' => 3
            ],
            [
                'type' => 'supplier',
                'ruc' => '12345678912',
                'businessName' => 'Supplier 1',
                'email' => 'supplier1@gmail.com',
                'phone' => '945678123',
                'representativeDni' => '12345678',
                'representativeNames' => 'Juan Perez',
                'country_id' => 1
            ],
            [
                'type' => 'supplier',
                'ruc' => '98765432101',
                'businessName' => 'Supplier 2',
                'email' => 'supplier2@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '87654321',
                'representativeNames' => 'Maria Perez',
                'country_id' => 2
            ],
            [
                'type' => 'supplier',
                'ruc' => '45678912345',
                'businessName' => 'Supplier 3',
                'email' => 'supplier3@gmail.com',
                'phone' => '987654321',
                'representativeDni' => '45678912',
                'representativeNames' => 'Pedro Perez',
                'country_id' => 3
            ],
        ];

        foreach ($array as $object) {
            Person::create($object);
        }
    }
}
