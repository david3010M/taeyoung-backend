<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdatePaymentConceptRequest",
 *     title="UpdatePaymentConceptRequest",
 *     @OA\Property(property="name", type="string", example="name"),
 *     @OA\Property(property="type", type="string", example="type")
 * )
 */
class UpdatePaymentConceptRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'nullable',
                'string',
                Rule::unique('payment_concepts', 'name')->whereNull('deleted_at')
                    ->ignore($this->route('paymentConcept')),
            ],
            'type' => 'nullable|string|in:Ingreso,Egreso',
        ];
    }
}
