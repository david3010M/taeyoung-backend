<?php

namespace App\Http\Requests;

class IndexAccountReceivableRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'date' => 'nullable|string',
            'amount' => 'nullable|string',
            'balance' => 'nullable|string',
            'status' => 'nullable|string',
            'client_id' => 'nullable|integer',
            'client$filterName' => 'nullable|string',
            'client$country_id' => 'nullable|integer',
            'order_id' => 'nullable|integer',
            'currency$date' => 'nullable|string',
        ];
    }
}
