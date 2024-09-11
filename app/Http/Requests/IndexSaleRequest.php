<?php

namespace App\Http\Requests;

class IndexSaleRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'documentType' => 'nullable|string|in:BOLETA,FACTURA',
            'number' => 'nullable|string',
            'date' => 'nullable|array|size:2',
            'date.0' => 'nullable|date_format:Y-m-d',
            'date.1' => 'nullable|date_format:Y-m-d',
            'client_id' => 'nullable|integer',
            'client$filterName' => 'nullable|string',
            'client$country_id' => 'nullable|integer',
        ];
    }
}
