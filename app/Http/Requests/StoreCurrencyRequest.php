<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreCurrencyRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'currencyFrom' => 'required|string|in:PEN,USD',
            'currencyTo' => 'required|string|in:PEN,USD|different:currencyFrom',
            'rate' => 'required|numeric',
            'date' => [
                'required',
                'date',
                Rule::unique('currencies')->whereNull('deleted_at')
            ]
        ];
    }
}
