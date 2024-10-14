<?php

namespace Database\Seeders;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountPayableSeeder extends Seeder
{
    public function run(): void
    {
        $purchases = Order::where('type', 'purchase')->get();

        foreach ($purchases as $purchase) {
            $quotaPerDues = round($purchase->total / 3, 2);
            for ($i = 0; $i < 3; $i++) {
                if ($i == 2) {
                    $ammount = $purchase->total - ($quotaPerDues * 2);
                } else {
                    $ammount = $quotaPerDues;
                }
                AccountPayable::factory()->create([
                    'paymentType' => 'CREDITO',
                    'order_id' => $purchase->id,
                    'supplier_id' => $purchase->supplier_id,
                    'date' => $purchase->date,
                    'days' => $i * 30,
                    'amount' => $ammount,
                    'balance' => $ammount,
                ]);
            }
        }

    }
}
