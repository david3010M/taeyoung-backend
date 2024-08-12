<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => "COTI-" . $this->number,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'detail' => $this->detail,

            'paymentType' => $this->paymentType,
            'currencyType' => $this->currencyType,
            'totalMachinery' => round($this->totalMachinery, 2),
            'totalSpareParts' => round($this->totalSpareParts, 2),
            'subtotal' => round($this->subtotal, 2),
            'igv' => round($this->igv, 2),
            'discount' => round($this->discount, 2),
            'total' => round($this->total, 2),
            'client_id' => $this->client_id,
            'client' => (new ClientResource($this->client))->businessName,
            'detailMachinery' => DetailMachineryResource::collection($this->detailMachinery),
            'detailSpareParts' => DetailSparePartResource::collection($this->detailSpareParts),
        ];
    }
}
