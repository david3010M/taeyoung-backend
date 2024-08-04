<?php

namespace Database\Seeders;

use App\Models\DetailMachinery;
use App\Models\Quotation;
use Illuminate\Database\Seeder;

class DetailMachinerySeeder extends Seeder
{
    public function run(): void
    {
//        PURCHASE
        $quotations = Quotation::all();
        foreach ($quotations as $quotation) {
            $detailMachinery = DetailMachinery::factory()->purchase()->create([
                'quotation_id' => $quotation->id,
                'movementType' => 'purchase',
                'purchasePrice' => rand(100, 1000),
            ]);
            $price = $detailMachinery->purchasePrice * $detailMachinery->quantity;
            $quotation->update([
                'price' => $price + $quotation->price,
            ]);
        }
    }
}
