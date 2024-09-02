<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="SparePartResource",
 *     title="SparePartResource",
 *     @OA\Property(property="id", type="integer", example="1" ),
 *     @OA\Property(property="code", type="string", example="123456" ),
 *     @OA\Property(property="name", type="string", example="Spare Part" ),
 *     @OA\Property(property="purchasePrice", type="number", example="100.00" ),
 *     @OA\Property(property="salePrice", type="number", example="150.00" ),
 *     @OA\Property(property="purchaseValue", type="number", example="1000.00" ),
 *     @OA\Property(property="saleValue", type="number", example="1500.00" ),
 *     @OA\Property(property="stock", type="integer", example="10" ),
 *     @OA\Property(property="unit_id", type="integer", example="1" ),
 *     @OA\Property(property="unit", ref="#/components/schemas/UnitResource")
 * )
 *
 * @OA\Schema (
 *     schema="SparePartPagination",
 *     title="SparePartPagination",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SparePartResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 */
class SparePartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => 'REPU-' . $this->code,
            'name' => $this->name,
            'purchasePrice' => $this->purchasePrice ? round($this->purchasePrice, 2) : null,
            'salePrice' => $this->salePrice ? round($this->salePrice, 2) : null,
            'purchaseValue' => $this->purchaseValue ? round($this->purchaseValue, 2) : null,
            'saleValue' => $this->saleValue ? round($this->saleValue, 2) : null,
            'stock' => $this->stock,
            'unit_id' => $this->unit_id,
            'unit' => $this->unit,
        ];
    }
}
