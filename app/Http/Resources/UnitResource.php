<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 *     schema="UnitResource",
 *     title="UnitResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Unit 1"),
 *     @OA\Property(property="abbreviation", type="string", example="UN")
 * )
 *
 * @OA\Schema(
 *     schema="UnitCollection",
 *     title="UnitCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/UnitResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 */
class UnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
