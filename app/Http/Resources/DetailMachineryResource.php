<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="DetailMachineryResource",
 *     title="DetailMachineryResource",
 *     description="Detail Machinery resource",
 *     @OA\Property (property="id", type="integer", example="1"),
 *     @OA\Property (property="description", type="string", example="This is a description"),
 *     @OA\Property (property="quantity", type="integer", example="1"),
 *     @OA\Property (property="movementType", type="string", example="Ingreso"),
 *     @OA\Property (property="purchasePrice", type="number", example="100"),
 *     @OA\Property (property="salePrice", type="number", example="100"),
 *     @OA\Property (property="purchaseValue", type="number", example="100"),
 *     @OA\Property (property="saleValue", type="number", example="100"),
 *     @OA\Property (property="order_id", type="integer", example="1"),
 *     @OA\Property (property="quotation_id", type="integer", example="21")
 * )
 */
class DetailMachineryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'movementType' => $this->movementType,
            'purchasePrice' => $this->purchasePrice ? round($this->purchasePrice, 2) : null,
            'salePrice' => $this->salePrice ? round($this->salePrice, 2) : null,
            'purchaseValue' => $this->purchaseValue ? round($this->purchaseValue, 2) : null,
            'saleValue' => $this->saleValue ? round($this->saleValue, 2) : null,
            'order_id' => $this->order_id,
            'quotation_id' => $this->quotation_id
        ];
    }
}
