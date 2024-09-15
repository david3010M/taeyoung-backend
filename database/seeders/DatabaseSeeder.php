<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CountrySeeder::class);
        $this->call(GroupMenuSeeder::class);
        $this->call(TypeUserSeeder::class);
        $this->call(OptionMenuSeeder::class);
        $this->call(AccessSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(SparePartSeeder::class);
//        $this->call(FileSeeder::class);
        $this->call(QuotationSeeder::class);
        $this->call(DetailSparePartSeeder::class);
        $this->call(DetailMachinerySeeder::class);
        $this->call(OrderSeeder::class);
        $this->call(PaymentConceptSeeder::class);
        $this->call(AccountReceivableSeeder::class);


    }
}
