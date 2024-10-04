<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="DepartmentResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Pendidikan"),
 *     @OA\Property(property="provinces", type="array", @OA\Items(ref="#/components/schemas/ProvinceResource"))
 * )
 *
 * @OA\Schema (
 *     schema="DepartmentCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/DepartmentResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
 * )
 *
 */
class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'provinces' => ProvinceResource::collection($this->provinces)
        ];
    }
}
