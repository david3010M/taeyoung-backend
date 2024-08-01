<?php

namespace App\Http\Requests;

class StoreCurrencyRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'currencyFrom' => 'required|string|in:PEN,USD',
            'currencyTo' => 'required|string|in:PEN,USD',
            'rate' => 'required|numeric',
            'date' => 'required|date',
        ];
    }
}
