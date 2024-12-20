<?php

namespace App\Http\Requests;


use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreSupplierRequest",
 *     title="StoreSupplierRequest",
 *     @OA\Property(property="typeDocument", type="string", example="DNI"),
 *     @OA\Property(property="dni", type="string", example="12345678"),
 *     @OA\Property(property="ruc", type="string", example="12345678901"),
 *     @OA\Property(property="names", type="string", example="Juan"),
 *     @OA\Property(property="fatherSurname", type="string", example="Perez"),
 *     @OA\Property(property="motherSurname", type="string", example="Gomez"),
 *     @OA\Property(property="businessName", type="string", example="Empresa SAC"),
 *     @OA\Property(property="address", type="string", example="Av. Los Pinos 123"),
 *     @OA\Property(property="email", type="string", example="mail@mail.com"),
 *     @OA\Property(property="phone", type="string", example="987654321"),
 *     @OA\Property(property="representativeDni", type="string", example="12345678"),
 *     @OA\Property(property="representativeNames", type="string", example="Juan"),
 *     @OA\Property(property="country_id", type="integer", example="1")
 * )
 */
class StoreSupplierRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'typeDocument' => 'required|string|in:DNI,RUC,OTRO',
            'document' => [
                'nullable',
                'string',
                Rule::unique('people', 'document')
                    ->where('type', 'supplier')
                    ->whereNull('deleted_at')
            ],
            'dni' => [
                'nullable',
                'string',
                'min:8',
                'max:8',
                Rule::unique('people', 'dni')
                    ->where('type', 'supplier')
                    ->whereNull('deleted_at')
            ],
            'ruc' => [
                'nullable',
                'string',
                'min:11',
                'max:11',
                Rule::unique('people', 'ruc')->whereNull('deleted_at')
            ],
            'names' => 'nullable|requiredIf:typeDocument,DNI|string',
            'fatherSurname' => 'nullable|requiredIf:typeDocument,DNI|string',
            'motherSurname' => 'nullable|requiredIf:typeDocument,DNI|string',
            'businessName' => 'nullable|requiredIf:typeDocument,RUC|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|integer',
            'representativeDni' => 'nullable|string|min:8|max:8',
            'representativeNames' => 'nullable|string',
            'country_id' => 'required|integer',
        ];
    }
}
