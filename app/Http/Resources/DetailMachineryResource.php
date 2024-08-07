<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailMachineryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * "id": 20,
         * "description": "Facere quaerat natus est repellat dolore libero provident.",
         * "quantity": 8,
         * "movementType": "purchase",
         * "purchasePrice": null,
         * "salePrice": "283.00",
         * "order_id": 20,
         * "quotation_id": 20
         */
        return [
            'id' => $this->id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'movementType' => $this->movementType,
            'purchasePrice' => $this->purchasePrice ? round($this->purchasePrice, 2) : null,
            'salePrice' => $this->salePrice ? round($this->salePrice) : null,
            'order_id' => $this->order_id,
            'quotation_id' => $this->quotation_id
        ];
    }
}
