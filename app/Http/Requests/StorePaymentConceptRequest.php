<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StorePaymentConceptRequest",
 *     title="StorePaymentConceptRequest",
 *     required={"name", "type"},
 *     @OA\Property(property="name", type="string", example="name"),
 *     @OA\Property(property="type", type="string", example="type")
 * )
 */
class StorePaymentConceptRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('payment_concepts', 'name')->whereNull('deleted_at'),
            ],
            'type' => 'required|string|in:Ingreso,Egreso',
        ];
    }
}
