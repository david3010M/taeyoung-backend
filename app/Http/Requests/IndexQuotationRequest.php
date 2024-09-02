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
            'client$filterName' => 'nullable|string',
            'date' => 'nullable|array|size:2',
            'date.0' => 'nullable|date_format:Y-m-d',
            'date.1' => 'nullable|date_format:Y-m-d',
            'sort' => 'nullable|string|in:date,currencyFrom,currencyTo,id,number',
            'direction' => 'nullable|string|in:asc,desc',
        ];
    }
}
