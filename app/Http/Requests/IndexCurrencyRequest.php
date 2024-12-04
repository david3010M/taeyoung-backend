<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexCurrencyRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'buyRate' => 'nullable|numeric',
            'saleRate' => 'nullable|numeric',
            'date' => 'nullable|date',
        ];
    }
}
