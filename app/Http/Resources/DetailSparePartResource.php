<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailSparePartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'movementType' => $this->movementType,
            'purchasePrice' => $this->purchasePrice ? round($this->purchasePrice, 2) : null,
            'salePrice' => $this->salePrice ? round($this->salePrice, 2) : null,
            'purchaseValue' => $this->purchaseValue ? round($this->purchaseValue, 2) : null,
            'saleValue' => $this->saleValue ? round($this->saleValue, 2) : null,
            'order_id' => $this->order_id,
            'quotation_id' => $this->quotation_id,
            'spare_part_id' => $this->spare_part_id,
            'sparePart' => new SparePartResource($this->sparePart),
        ];
    }
}
