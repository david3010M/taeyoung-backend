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
            $detailMachinery = DetailMachinery::factory()->quotation()->create([
                'quotation_id' => $quotation->id,
            ]);

            $totalMachinery = $detailMachinery->saleValue;
            $subtotal = $totalMachinery + $quotation->subtotal;
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;

            $quotation->update([
                'totalMachinery' => $totalMachinery,
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total
            ]);
        }
    }
}
