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
            $quotaPerDues = round($sale->total / 3, 2);
            for ($i = 0; $i < 3; $i++) {
                if ($i == 2) {
                    $ammount = $sale->total - ($quotaPerDues * 2);
                } else {
                    $ammount = $quotaPerDues;
                }
                AccountReceivable::factory()->create([
                    'paymentType' => 'CREDITO',
                    'order_id' => $sale->id,
                    'client_id' => $sale->client_id,
                    'date' => $sale->date,
                    'days' => $i * 30,
                    'amount' => $ammount,
                    'balance' => $ammount,
                ]);
            }
        }

    }
}
