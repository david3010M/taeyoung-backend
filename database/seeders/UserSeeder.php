<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['names' => 'Admin', 'lastnames' => 'Taeyoung', 'username' => 'admin', 'password' => Hash::make('adminTaeyoung'), 'typeuser_id' => 1],
            ['names' => 'User', 'lastnames' => 'Taeyoung', 'username' => 'user', 'password' => Hash::make('userTaeyoung'), 'typeuser_id' => 1]
        ];

        foreach ($array as $value) {
            User::create($value);
        }
    }
}
