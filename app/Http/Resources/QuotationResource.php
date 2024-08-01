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
//            'id' => $this->id,
            'number' => "COTI-" . $this->number,
            'date' => Carbon::parse($this->date)->format('d-m-Y'),
            'client' => (new ClientResource($this->client))->businessName,
            'detail' => $this->detail,
            'initialPrice' => $this->initialPrice,
            'paymentRemainder' => $this->paymentRemainder,
            'price' => $this->price,
            'currencyType' => $this->currencyType,
            'exchangeRate' => $this->exchangeRate,
            'currency_id' => $this->currency_id,
            'client_id' => $this->client_id,
        ];
    }
}
