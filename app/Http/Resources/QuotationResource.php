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
            'detail' => $this->detail,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'currencyType' => $this->currencyType,
            'price' => round($this->price, 2),
            'initialPayment' => round($this->initialPayment, 2),
            'balance' => round($this->balance, 2),
            'debts' => (int)$this->debts,
            'exchangeRate' => $this->exchangeRate ? round($this->exchangeRate, 2) : null,
            'currency_id' => $this->currency_id,
            'client_id' => $this->client_id,
            'client' => (new ClientResource($this->client))->businessName,
            'detailMachinery' => DetailMachineryResource::collection($this->detailMachinery),
            'detailSpareParts' => DetailSparePartResource::collection($this->detailSpareParts),
            'currency' => new CurrencyResource($this->currency),
        ];
    }
}
