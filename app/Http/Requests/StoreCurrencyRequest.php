<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreCurrencyRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date',
                Rule::unique('currencies')->whereNull('deleted_at')
            ],
            'buyRate' => 'required|numeric',
            'saleRate' => 'required|numeric',
        ];
    }
}
