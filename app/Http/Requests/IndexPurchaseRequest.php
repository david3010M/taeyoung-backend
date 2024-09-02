<?php

namespace App\Http\Requests;

class IndexPurchaseRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable|string',
            'date' => 'nullable|array|size:2',
            'date.0' => 'nullable|date_format:Y-m-d',
            'date.1' => 'nullable|date_format:Y-m-d',
            'supplier_id' => 'nullable|integer',
            'supplier$filterName' => 'nullable|string',
            'supplier$country_id' => 'nullable|integer',
        ];
    }
}
