<?php

namespace Database\Seeders;

use App\Models\DetailMachinery;
use App\Models\DetailSparePart;
use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailSparePartSeeder extends Seeder
{
    public function run(): void
    {
//        PURCHASE
        $quotations = Quotation::all();
        foreach ($quotations as $quotation) {
            $detailSparePart = DetailSparePart::factory()->purchase()->create([
                'quotation_id' => $quotation->id,
            ]);
            $price = $detailSparePart->purchasePrice * $detailSparePart->quantity;
            $quotation->update([
                'price' => $price + $quotation->price,
            ]);
        }
    }
}
