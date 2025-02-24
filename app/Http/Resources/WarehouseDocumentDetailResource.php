<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="WarehouseDocumentDetailResource",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="machinery_id", type="integer", nullable=true, example=null),
 *   @OA\Property(property="spare_part_id", type="integer", nullable=true, example=5),
 *   @OA\Property(property="quantity", type="integer", example=10),
 *   @OA\Property(property="machinery", type="object", ref="#/components/schemas/MachineryResource"),
 *   @OA\Property(property="sparePart", type="object", ref="#/components/schemas/SparePartResource")
 * )
 */
class WarehouseDocumentDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'machinery_id'  => $this->machinery_id,
            'spare_part_id' => $this->spare_part_id,
            'quantity'      => $this->quantity,

            // Relación con Machinery y SparePart
            'machinery'     => $this->machinery ? new MachineryResource($this->machinery) : null,
            'sparePart'     => $this->sparePart ? new SparePartResource($this->sparePart) : null,
        ];
    }
}
