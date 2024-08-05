<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexQuotationRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable|string',
            'currencyFrom' => 'nullable|string|in:USD,PEN',
            'currencyTo' => 'nullable|string|in:USD,PEN',
            'client_businessName' => 'nullable|string',
            'date' => 'nullable|date',
            'sort' => 'nullable|string|in:date,currencyFrom,currencyTo,id,number',
            'direction' => 'nullable|string|in:asc,desc',
        ];
    }
}
