<?php

namespace Database\Seeders;

use App\Models\AccountReceivable;
use App\Models\Order;
use Illuminate\Database\Seeder;

class AccountReceivableSeeder extends Seeder
{
    public function run(): void
    {
        $sales = Order::where('type', 'sale')->get();

        foreach ($sales as $sale) {
            for ($i = 0; $i < 3; $i++) {
                AccountReceivable::factory()->create([
                    'order_id' => $sale->id,
                    'client_id' => $sale->client_id,
                    'date' => $sale->date,
                    'days' => $i * 30,
                    'amount' => $sale->total,
                    'balance' => $sale->total,
                ]);
            }
        }

    }
}
