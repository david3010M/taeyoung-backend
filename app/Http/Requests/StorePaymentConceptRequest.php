<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
