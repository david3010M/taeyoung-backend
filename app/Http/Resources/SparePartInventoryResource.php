<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="SparePartInventoryResource",
 *   title="SparePartInventoryResource",
 *   description="Recurso de inventario de repuestos con último precio real/contable",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="spare_part_id", type="integer", example=15),
 *   @OA\Property(property="real_stock", type="integer", example=100),
 *   @OA\Property(property="contable_stock", type="integer", example=90),
 *   @OA\Property(
 *     property="sparePart",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=15),
 *     @OA\Property(property="code", type="string", example="REP-999"),
 *     @OA\Property(property="name", type="string", example="Filtro de aire"),
 *     @OA\Property(property="purchasePrice", type="number", format="float", example=30.50),
 *     @OA\Property(property="salePrice", type="number", format="float", example=45.99)
 *   ),
 *   @OA\Property(property="lastRealPrice", type="number", format="float", example=35.0),
 *   @OA\Property(property="lastContablePrice", type="number", format="float", example=40.0)
 * )
 */
class SparePartInventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $this representa un SparePartInventory
        $sparePart      = $this->sparePart;
        $latestPurchase = $sparePart?->latestPurchaseDetail; // hasOne con movementType='purchase'

        return [
            'id'             => $this->id,
            'spare_part_id'  => $this->spare_part_id,
            'real_stock'     => $this->real_stock,
            'contable_stock' => $this->contable_stock,

            'sparePart' => [
                'id'            => $sparePart?->id,
                'code'          => $sparePart?->code,
                'name'          => $sparePart?->name,
                'purchasePrice' => $sparePart?->purchasePrice,
                'salePrice'     => $sparePart?->salePrice,
            ],

            'lastRealPrice'     => $latestPurchase?->realPrice,
            'lastContablePrice' => $latestPurchase?->contablePrice,
        ];
    }
}
