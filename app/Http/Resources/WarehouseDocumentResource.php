<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="WarehouseDocumentResource",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="date", type="string", format="date", example="2025-02-25"),
 *   @OA\Property(property="number", type="string", example="DOC-001"),
 *   @OA\Property(property="documentType", type="string", enum={"ingreso","salida"}, example="ingreso"),
 *   @OA\Property(property="mode", type="string", enum={"Real","Contable"}, example="Real"),
 *   @OA\Property(property="reason", type="string", example="Ajuste de stock"),
 *   @OA\Property(property="comment", type="string", example="Observaciones varias"),
 *   @OA\Property(
 *     property="details",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/WarehouseDocumentDetailResource")
 *   )
 * )
 */
class WarehouseDocumentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'date'         => $this->date->format('Y-m-d'),
            'number'       => $this->number,
            'documentType' => $this->documentType,
            'mode'         => $this->mode,
            'reason'       => $this->reason,
            'comment'      => $this->comment,

            'details' => WarehouseDocumentDetailResource::collection($this->whenLoaded('details')),
        ];
    }
}
