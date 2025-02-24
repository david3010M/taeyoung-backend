<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *   schema="SaleGuideResource",
 *   title="SaleGuideResource",
 *   description="Recurso que representa una Guía de Venta",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="number", type="string", example="GUIDE-001"),
 *   @OA\Property(property="date", type="string", format="date", example="2025-02-25"),
 *   @OA\Property(property="documentType", type="string", example="GUIA"),
 *   @OA\Property(property="client_id", type="integer", example=10),
 *   @OA\Property(property="client", type="object", ref="#/components/schemas/Client"),
 *   @OA\Property(property="detailMachinery", type="array", @OA\Items(ref="#/components/schemas/DetailMachineryResource")),
 *   @OA\Property(property="detailSpareParts", type="array", @OA\Items(ref="#/components/schemas/DetailSparePartResource")),
 *   @OA\Property(property="comment", type="string", example="Observaciones varias"),
 *   @OA\Property(property="subtotal", type="number", example=100),
 *   @OA\Property(property="total", type="number", example=100),
 * )
 */
class SaleGuideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'number'        => $this->number,
            'date'          => Carbon::parse($this->date)->format('Y-m-d'),
            'documentType'  => $this->documentType,
            'client_id'     => $this->client_id,
            'client'        => $this->client ? new ClientResource($this->client) : null,

            'detailMachinery'  => DetailMachineryResource::collection($this->detailMachinery),
            'detailSpareParts' => DetailSparePartResource::collection($this->detailSpareParts),

            'comment'       => $this->comment,
            'subtotal'      => round($this->subtotal,2),
            'total'         => round($this->total,2),
        ];
    }
}
