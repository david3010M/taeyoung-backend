<?php

namespace Database\Seeders;

use App\Models\DetailSparePart;
use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailSparePartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        foreach ($orders as $order) {
            DetailSparePart::factory()->create([
                'order_id' => $order->id,
                'quotation_id' => null,
            ]);
        }

        $quotations = Quotation::all();
        foreach ($quotations as $quotation) {
            DetailSparePart::factory()->create([
                'order_id' => null,
                'quotation_id' => $quotation->id,
            ]);
        }
    }
}
