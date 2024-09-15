<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountPayableSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::where('type', 'purchase')->get();
    }
}
