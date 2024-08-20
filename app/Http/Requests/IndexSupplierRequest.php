<?php

namespace App\Http\Requests;


class IndexSupplierRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'ruc' => 'nullable|integer',
            'businessName' => 'nullable|string',
            'email' => 'nullable|string',
            'phone' => 'nullable|integer',
            'countryId' => 'nullable|string',
        ];
    }
}
