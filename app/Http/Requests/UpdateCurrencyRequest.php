<?php

namespace App\Http\Requests;

class UpdateCurrencyRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'currencyFrom' => 'nullable|string|in:PEN,USD',
            'currencyTo' => 'nullable|string|in:PEN,USD|different:currencyFrom',
            'rate' => 'nullable|numeric',
            'date' => 'nullable|date',
        ];
    }
}
