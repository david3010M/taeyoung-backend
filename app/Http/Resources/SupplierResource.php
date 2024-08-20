<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema (
 *      schema="Supplier",
 *      title="Supplier",
 *      description="Supplier model",
 *      @OA\Property(property="id", type="integer", example="1"),
 *      @OA\Property(property="type", type="string", example="supplier"),
 *      @OA\Property(property="ruc", type="string", example="20547869541"),
 *      @OA\Property(property="businessName", type="string", example="Distribuidora de Productos S.A."),
 *      @OA\Property(property="email", type="string", example="supplier@gmail.com"),
 *      @OA\Property(property="phone", type="string", example="987654321"),
 *      @OA\Property(property="representativeDni", type="string", example="12345678"),
 *      @OA\Property(property="representativeNames", type="string", example="Juan Perez"),
 *      @OA\Property(property="country_id", type="integer", example="1"),
 *      @OA\Property(property="country", type="object", ref="#/components/schemas/Country")
 *  )
 *
 * @OA\Schema(
 *      schema="SupplierResource",
 *      required={"id", "ruc", "businessName", "email", "phone", "country"},
 *      @OA\Property(property="id", type="integer", example="1"),
 *      @OA\Property(property="ruc", type="string", example="12345678901"),
 *      @OA\Property(property="businessName", type="string", example="Business Name"),
 *      @OA\Property(property="email", type="string", example="mail@gmail.com"),
 *      @OA\Property(property="phone", type="integer", example="123456789"),
 *      @OA\Property(property="country", type="string", example="Peru")
 *  )
 *
 *
 * @OA\Schema(
 *     schema="SupplierPagination",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SupplierResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema (
 *      schema="SupplierRequest",
 *      title="SupplierRequest",
 *      description="Supplier request",
 *      required={"ruc", "businessName", "country_id"},
 *      @OA\Property(property="ruc", type="string", example="20547869541"),
 *      @OA\Property(property="businessName", type="string", example="Distribuidora de Productos S.A."),
 *      @OA\Property(property="email", type="string", example="supplier@gmail.com"),
 *      @OA\Property(property="phone", type="string", example="987654321"),
 *      @OA\Property(property="representativeDni", type="string", example="12345678"),
 *      @OA\Property(property="representativeNames", type="string", example="Juan Perez"),
 *      @OA\Property(property="country_id", type="integer", example="1")
 *  )
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
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'representativeDni' => $this->typeDocument === 'RUC' ? $this->representativeDni : null,
            'representativeNames' => $this->typeDocument === 'RUC' ? $this->representativeNames : null,
            'country' => (new CountryResource($this->country))->name,
        ];
    }
}
