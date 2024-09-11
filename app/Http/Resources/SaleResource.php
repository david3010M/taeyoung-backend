<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'detail' => $this->detail,

            'documentType' => $this->documentType,
            'paymentType' => $this->paymentType,
            'currencyType' => $this->currencyType,
            'totalMachinery' => round($this->totalMachinery, 2),
            'totalSpareParts' => round($this->totalSpareParts, 2),

            'subtotal' => round($this->subtotal, 2),
            'igv' => round($this->igv, 2),
            'discount' => round($this->discount, 2),
            'total' => round($this->total, 2),

            'totalIncome' => round($this->totalIncome, 2),
            'totalExpense' => round($this->totalExpense, 2),

            'comment' => $this->comment,

            'supplier_id' => $this->supplier_id,
            'quotation_id' => $this->quotation_id,

            'supplier' => new SupplierResource($this->supplier),
            'quotation' => new SingleQuotationResource($this->quotation),
            'detailMachinery' => DetailMachineryResource::collection($this->detailMachinery),
            'detailSpareParts' => DetailSparePartResource::collection($this->detailSpareParts),
        ];
    }
}
