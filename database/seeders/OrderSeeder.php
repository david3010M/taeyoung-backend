<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
//        PURCHASE
        $quotations = Quotation::all();

        foreach ($quotations as $quotation) {
            $total = $quotation->total;
            $totalIncome = $total;
            $totalExpense = $total;
            $quotation->update([
                'total' => $totalIncome,
            ]);

            $order = Order::factory()->create(
                [
                    'type' => 'sale',
                    'quantity' => 1,
                    'totalIncome' => $totalIncome,
                    'totalExpense' => $totalExpense,
                    'currency' => $quotation->currencyType,
                    'quotation_id' => $quotation->id,
                ]
            );

            $quotation->detailSpareParts()->update(['order_id' => $order->id]);
            $quotation->detailMachinery()->update(['order_id' => $order->id]);
        }

//        Order::factory()->count(50)->machineryPurchase()->create();
//        Order::factory()->count(50)->machinerySale()->create();
//        Order::factory()->count(50)->sparePartPurchase()->create();
//        Order::factory()->count(50)->sparePartSale()->create();
    }
}
