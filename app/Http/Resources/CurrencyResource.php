<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CurrencyResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="buyRate", type="number", example="20.00"),
 *     @OA\Property(property="saleRate", type="number", example="21.00"),
 *     @OA\Property(property="date", type="string", format="date", example="2021-01-01")
 * )
 *
 * @OA\Schema (
 *     schema="CurrencyCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CurrencyResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class CurrencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'buyRate' => round($this->buyRate, 2),
            'saleRate' => round($this->saleRate, 2),
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ];
    }
}
