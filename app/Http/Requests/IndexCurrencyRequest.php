<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexCurrencyRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'currencyFrom' => 'nullable|string',
            'currencyTo' => 'nullable|string',
            'rate' => 'nullable|numeric',
            'date' => 'nullable|date',
        ];
    }
}
