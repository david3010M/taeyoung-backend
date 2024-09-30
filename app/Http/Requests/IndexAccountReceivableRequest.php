<?php

namespace App\Http\Requests;

class IndexAccountReceivableRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'paymentType' => 'nullable|string|in:CONTADO,CREDITO',
            'date' => 'nullable|array|size:2',
            'date.0' => 'nullable|date_format:Y-m-d',
            'date.1' => 'nullable|date_format:Y-m-d',
            'amount' => 'nullable|string',
            'balance' => 'nullable|string',
            'status' => 'nullable|string|in:PENDIENTE,PAGADO,VENCIDO',
            'client_id' => 'nullable|integer',
            'client$filterName' => 'nullable|string',
            'client$country_id' => 'nullable|integer',
            'order_id' => 'nullable|integer',
            'currency$date' => 'nullable|string',
        ];
    }
}
