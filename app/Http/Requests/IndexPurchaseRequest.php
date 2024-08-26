<?php

namespace App\Http\Requests;

class IndexPurchaseRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'string',
            'date' => 'date',
            'supplier_id' => 'integer',
        ];
    }
}
