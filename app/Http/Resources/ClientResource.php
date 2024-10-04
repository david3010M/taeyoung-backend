<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Client",
 *     required={"id", "ruc", "businessName", "email", "phone", "country"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="type", type="string", example="RUC"),
 *     @OA\Property(property="dni", type="string", example="12345678"),
 *     @OA\Property(property="ruc", type="string", example="12345678901"),
 *     @OA\Property(property="names", type="string", example="Names"),
 *     @OA\Property(property="fatherSurname", type="string", example="Father Surname"),
 *     @OA\Property(property="motherSurname", type="string", example="Mother Surname"),
 *     @OA\Property(property="businessName", type="string", example="Business Name"),
 *     @OA\Property(property="filterName", type="string", example="Filter Name"),
 *     @OA\Property(property="address", type="string", example="Address"),
 *     @OA\Property(property="email", type="string", example="mail@gmail.com"),
 *     @OA\Property(property="phone", type="integer", example="123456789"),
 *     @OA\Property(property="representativeDni", type="string", example="12345678"),
 *     @OA\Property(property="representativeNames", type="string", example="Representative Names"),
 *     @OA\Property(property="country", type="string", example="Peru"),
 *     @OA\Property(property="province", type="string", example="Lima"),
 * )
 *
 * @OA\Schema(
 *     schema="ClientCollection",
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
            'typeDocument' => $this->typeDocument,
            'dni' => $this->typeDocument === 'DNI' ? $this->dni : null,
            'ruc' => $this->typeDocument === 'RUC' ? $this->ruc : null,
            'names' => $this->typeDocument === 'DNI' ? $this->names : null,
            'fatherSurname' => $this->typeDocument === 'DNI' ? $this->fatherSurname : null,
            'motherSurname' => $this->typeDocument === 'DNI' ? $this->motherSurname : null,
            'businessName' => $this->typeDocument === 'RUC' ? $this->businessName : null,
            'filterName' => $this->filterName,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'representativeDni' => $this->typeDocument === 'RUC' ? $this->representativeDni : null,
            'representativeNames' => $this->typeDocument === 'RUC' ? $this->representativeNames : null,
            'country' => (new CountryResource($this->country))->name,
            'province' => $this->province ? (new ProvinceResource($this->province))->name : null,
        ];
    }
}
