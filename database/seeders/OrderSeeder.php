<?php

namespace Database\Seeders;

use App\Models\DetailMachinery;
use App\Models\DetailSparePart;
use App\Models\Order;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
//        PURCHASE
        $quotations = Quotation::all();
        foreach ($quotations as $quotation) {
            $order = Order::factory()->purchase()->create([
                'supplier_id' => $quotation->client_id,
                'date' => Carbon::parse($quotation->date)->addDays(15),
            ]);
            $detailSpareParts = $quotation->detailSpareParts;
            $detailMachinery = $quotation->detailMachinery;
            foreach ($detailSpareParts as $detail) {
                DetailSparePart::create(
                    [
                        'quantity' => $detail->quantity,
                        'movementType' => 'purchase',
                        'purchasePrice' => $detail->purchasePrice,
                        'salePrice' => null,
                        'purchaseValue' => $detail->purchaseValue,
                        'saleValue' => null,
                        'spare_part_id' => $detail->spare_part_id,
                        'quotation_id' => $detail->quotation_id,
                        'order_id' => $order->id,
                    ]
                );
            }

            foreach ($detailMachinery as $detail) {
                DetailMachinery::create(
                    [
                        'description' => $detail->description,
                        'quantity' => $detail->quantity,
                        'movementType' => 'purchase',
                        'purchasePrice' => $detail->purchasePrice,
                        'salePrice' => null,
                        'purchaseValue' => $detail->purchaseValue,
                        'saleValue' => null,
                        'quotation_id' => $detail->quotation_id,
                        'order_id' => $order->id,
                    ]
                );
            }

            $order->update([
                'totalMachinery' => $order->detailMachinery->sum('purchaseValue'),
                'totalSpareParts' => $order->detailSpareParts->sum('purchaseValue'),
                'total' => $order->detailMachinery->sum('purchaseValue') + $order->detailSpareParts->sum('purchaseValue'),
                'quotation_id' => $quotation->id,
                'totalExpense' => $order->detailMachinery->sum('purchaseValue') + $order->detailSpareParts->sum('purchaseValue'),
            ]);
        }

//        SALE
        foreach ($quotations as $quotation) {
            $order = Order::factory()->sale()->create([
                'client_id' => $quotation->client_id,
                'date' => Carbon::parse($quotation->date)->addDays(30),
            ]);
            $detailSpareParts = $quotation->detailSpareParts;
            $detailMachinery = $quotation->detailMachinery;
            foreach ($detailSpareParts as $detail) {
                DetailSparePart::create(
                    [
                        'quantity' => $detail->quantity,
                        'movementType' => 'sale',
                        'purchasePrice' => $detail->purchasePrice,
                        'salePrice' => $detail->salePrice,
                        'purchaseValue' => $detail->purchaseValue,
                        'saleValue' => $detail->saleValue,
                        'spare_part_id' => $detail->spare_part_id,
                        'quotation_id' => $detail->quotation_id,
                        'order_id' => $order->id,
                    ]
                );
            }

            foreach ($detailMachinery as $detail) {
                DetailMachinery::create(
                    [
                        'description' => $detail->description,
                        'quantity' => $detail->quantity,
                        'movementType' => 'sale',
                        'purchasePrice' => $detail->purchasePrice,
                        'salePrice' => $detail->salePrice,
                        'purchaseValue' => $detail->purchaseValue,
                        'saleValue' => $detail->saleValue,
                        'quotation_id' => $detail->quotation_id,
                        'order_id' => $order->id,
                    ]
                );
            }

            $order->update([
                'totalMachinery' => $order->detailMachinery->sum('saleValue'),
                'totalSpareParts' => $order->detailSpareParts->sum('saleValue'),
                'total' => $order->detailMachinery->sum('saleValue') + $order->detailSpareParts->sum('saleValue'),
                'quotation_id' => $quotation->id,
                'totalIncome' => $order->detailMachinery->sum('saleValue') + $order->detailSpareParts->sum('saleValue'),
            ]);
        }
    }
}
