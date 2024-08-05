<?php

namespace App\Http\Requests;

class IndexMachineryPurchaseRequest extends IndexRequest
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
