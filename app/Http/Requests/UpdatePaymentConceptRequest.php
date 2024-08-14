<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
