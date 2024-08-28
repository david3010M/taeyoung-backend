<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 *     schema="SupplierResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="typeDocument", type="string", example="DNI"),
 *     @OA\Property(property="dni", type="string", example="12345678"),
 *     @OA\Property(property="ruc", type="string", example="12345678901"),
 *     @OA\Property(property="names", type="string", example="Juan"),
 *     @OA\Property(property="fatherSurname", type="string", example="Perez"),
 *     @OA\Property(property="motherSurname", type="string", example="Gomez"),
 *     @OA\Property(property="businessName", type="string", example="Empresa SAC"),
 *     @OA\Property(property="filterName", type="string", example="Juan Perez Gomez"),
 *     @OA\Property(property="address", type="string", example="Av. Los Pinos 123"),
 *     @OA\Property(property="email", type="string", example="mail@mail.com"),
 *     @OA\Property(property="phone", type="string", example="987654321"),
 *     @OA\Property(property="representativeDni", type="string", example="12345678"),
 *     @OA\Property(property="representativeNames", type="string", example="Juan"),
 *     @OA\Property(property="country", type="string", example="Peru"),
 *  )
 *
 * @OA\Schema(
 *     schema="SupplierCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SupplierResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class SupplierResource extends JsonResource
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
        ];
    }
}
