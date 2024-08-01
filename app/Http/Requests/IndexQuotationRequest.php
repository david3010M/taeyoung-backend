<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexQuotationRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'currencyFrom' => 'nullable|string|in:USD,PEN',
            'currencyTo' => 'nullable|string|in:USD,PEN',
            'date' => 'nullable|date',
        ];
    }
}
