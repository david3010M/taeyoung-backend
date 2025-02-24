<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="MachineryInventoryResource",
 *   title="MachineryInventoryResource",
 *   description="Recurso de inventario de maquinarias con último precio real/contable",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="machinery_id", type="integer", example=10),
 *   @OA\Property(property="real_stock", type="integer", example=100),
 *   @OA\Property(property="contable_stock", type="integer", example=90),
 *   @OA\Property(
 *     property="machinery",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="code", type="string", example="MCH-001"),
 *     @OA\Property(property="name", type="string", example="Excavadora"),
 *     @OA\Property(property="purchasePrice", type="number", format="float", example=50000.00),
 *     @OA\Property(property="salePrice", type="number", format="float", example=65000.00)
 *   ),
 *   @OA\Property(property="lastRealPrice", type="number", format="float", example=120.50),
 *   @OA\Property(property="lastContablePrice", type="number", format="float", example=130.00)
 * )
 */
class MachineryInventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $this representa un MachineryInventory
        $machinery      = $this->machinery;               // Relación belongsTo
        $latestPurchase = $machinery?->latestPurchaseDetail; // hasOne con movementType='purchase'

        return [
            'id'             => $this->id,
            'machinery_id'   => $this->machinery_id,
            'real_stock'     => $this->real_stock,
            'contable_stock' => $this->contable_stock,

            // Info básica de la maquinaria
            'machinery' => [
                'id'            => $machinery?->id,
                'code'          => $machinery?->code,
                'name'          => $machinery?->name,
                'purchasePrice' => $machinery?->purchasePrice,
                'salePrice'     => $machinery?->salePrice,
            ],

            // Ultimo precio real/contable tomado de detail_machineries
            'lastRealPrice'     => $latestPurchase?->realPrice,
            'lastContablePrice' => $latestPurchase?->contablePrice,
        ];
    }
}
