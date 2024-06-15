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
        $this->call(UserSeeder::class);

    }
}
