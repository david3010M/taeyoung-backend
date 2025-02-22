<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="DetailMachineryResource",
 *     title="DetailMachineryResource",
 *     description="Recurso que representa el detalle de maquinaria en una cotización u orden",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="description", type="string", example="Excavadora X"),
 *     @OA\Property(property="quantity", type="integer", example=1),
 *     @OA\Property(property="movementType", type="string", example="quotation"),
 *     @OA\Property(property="purchasePrice", type="number", format="float", example=100),
 *     @OA\Property(property="salePrice", type="number", format="float", example=150),
 *     @OA\Property(property="purchaseValue", type="number", format="float", example=100),
 *     @OA\Property(property="saleValue", type="number", format="float", example=150),
 *     @OA\Property(property="realPrice", type="number", format="float", example=120, description="Precio real de la maquinaria"),
 *     @OA\Property(property="contablePrice", type="number", format="float", example=130, description="Precio contable de la maquinaria"),
 *     @OA\Property(property="order_id", type="integer", nullable=true, example=10),
 *     @OA\Property(property="quotation_id", type="integer", nullable=true, example=21),
 *     @OA\Property(property="machinery_id", type="integer", example=5),
 *     @OA\Property(
 *         property="machinery",
 *         ref="#/components/schemas/MachineryResource",
 *         nullable=true,
 *         description="Objeto de maquinaria asociado (si existe)"
 *     )
 * )
 */
class DetailMachineryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'description'    => $this->description,
            'quantity'       => $this->quantity,
            'movementType'   => $this->movementType,
            'purchasePrice'  => $this->purchasePrice,
            'salePrice'      => $this->salePrice,
            'purchaseValue'  => $this->purchaseValue,
            'saleValue'      => $this->saleValue,
            'realPrice'      => $this->realPrice,
            'contablePrice'  => $this->contablePrice,
            'order_id'       => $this->order_id,
            'quotation_id'   => $this->quotation_id,
            'machinery_id'   => $this->machinery_id,
            // Relación con la maquinaria (anidada)
            'machinery'      => $this->machinery
                ? new MachineryResource($this->machinery)
                : null,
        ];
    }
}
