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
 *     @OA\Property(property="stock", type="integer", example="10" ),
 *     @OA\Property(property="image", type="string", example="http://example.com/image.jpg" )
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
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'purchasePrice' => $this->purchasePrice,
            'salePrice' => $this->salePrice,
            'stock' => $this->stock
        ];
    }
}
