<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MachineryResource",
 *     type="object",
 *     title="MachineryResource",
 *     required={"id", "name", "purchasePrice", "salePrice", "unit_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="MCH-001"),
 *     @OA\Property(property="name", type="string", example="Excavadora"),
 *     @OA\Property(property="purchasePrice", type="number", format="float", example=50000.00),
 *     @OA\Property(property="salePrice", type="number", format="float", example=65000.00),
 *     @OA\Property(property="unit_id", type="integer", example=1),
 *     @OA\Property(
 *         property="unit",
 *         type="object",
 *         description="Información de la unidad relacionada",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Unit 1"),
 *         @OA\Property(property="abbreviation", type="string", example="UN")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-21T12:12:33Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-21T12:12:33Z")
 * )
 *
 * @OA\Schema(
 *     schema="MachineryCollection",
 *     type="object",
 *     title="MachineryCollection",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/MachineryResource")
 *     ),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class MachineryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'code'          => 'MAQ-' . $this->code, // prefijo
            'name'          => $this->name,
            'purchasePrice' => $this->purchasePrice,
            'salePrice'     => $this->salePrice,
            'unit_id'       => $this->unit_id,
            // En tiempo de ejecución, se anida la unidad
            'unit'          => $this->unit
                ? [
                    'id'           => $this->unit->id,
                    'name'         => $this->unit->name,
                    'abbreviation' => $this->unit->abbreviation,
                ]
                : null,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
