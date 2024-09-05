<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PaymentConceptResource",
 *     title="PaymentConceptResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="number", type="string", example="123456"),
 *     @OA\Property(property="name", type="string", example="name"),
 *     @OA\Property(property="type", type="string", example="type")
 * )
 *
 * @OA\Schema(
 *     schema="PaymentConceptCollection",
 *     title="PaymentConceptCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PaymentConceptResource")),
 *      @OA\Property (property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *      @OA\Property (property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class PaymentConceptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => "CONC" . $this->number,
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}
