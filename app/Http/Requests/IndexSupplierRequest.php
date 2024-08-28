<?php

namespace App\Http\Requests;


class IndexSupplierRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'ruc' => 'nullable|integer',
            'filterName' => 'nullable|string',
            'email' => 'nullable|string',
            'phone' => 'nullable|integer',
            'country_id' => 'nullable|string',
        ];
    }
}
