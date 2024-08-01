<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Client",
 *     required={"id", "ruc", "businessName", "email", "phone", "country"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="ruc", type="string", example="12345678901"),
 *     @OA\Property(property="businessName", type="string", example="Business Name"),
 *     @OA\Property(property="email", type="string", example="mail@gmail.com"),
 *     @OA\Property(property="phone", type="integer", example="123456789"),
 *     @OA\Property(property="country", type="string", example="Peru")
 * )
 *
 * @OA\Schema(
 *     schema="ClientPagination",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Client")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
 * )
 *
 */
class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ruc' => $this->ruc,
            'businessName' => $this->businessName,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => (new CountryResource($this->country))->name,
        ];
    }
}
