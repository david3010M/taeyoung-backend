<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="DetailSparePartResource",
 *     title="DetailSparePartResource",
 *     description="Detail Spare Part resource",
 *     @OA\Property (property="id", type="integer", example="1"),
 *     @OA\Property (property="quantity", type="integer", example="1"),
 *     @OA\Property (property="movementType", type="string", example="Ingreso"),
 *     @OA\Property (property="purchasePrice", type="number", example="100"),
 *     @OA\Property (property="salePrice", type="number", example="100"),
 *     @OA\Property (property="purchaseValue", type="number", example="100"),
 *     @OA\Property (property="saleValue", type="number", example="100"),
 *     @OA\Property (property="order_id", type="integer", example="1"),
 *     @OA\Property (property="quotation_id", type="integer", example="21"),
 *     @OA\Property (property="spare_part_id", type="integer", example="1"),
 *     @OA\Property (property="sparePart", type="object", ref="#/components/schemas/SparePartResource")
 * )
 */
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
