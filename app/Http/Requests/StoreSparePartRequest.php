<?php

namespace App\Http\Requests;

class StoreSparePartRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'purchasePrice' => 'required|numeric',
            'salePrice' => 'required|numeric',
            'unit_id' => 'nullable|integer|exists:units,id',
        ];
    }
}
