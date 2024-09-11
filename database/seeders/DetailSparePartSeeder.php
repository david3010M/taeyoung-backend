<?php

namespace Database\Seeders;

use App\Models\DetailSparePart;
use App\Models\Quotation;
use Illuminate\Database\Seeder;

class DetailSparePartSeeder extends Seeder
{
    public function run(): void
    {
        $quotations = Quotation::all();
        foreach ($quotations as $quotation) {
            $detailSparePart = DetailSparePart::factory()->quotation()->create([
                'quotation_id' => $quotation->id,
            ]);
            $totalSparePart = $detailSparePart->saleValue;
            $subtotal = $totalSparePart + $quotation->subtotal;
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;

            $quotation->update([
                'totalSpareParts' => $totalSparePart,
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total
            ]);
        }
    }
}
