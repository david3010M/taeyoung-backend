<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCurrencyRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'date' => [
                'nullable',
                'date',
                Rule::unique('currencies')->whereNull('deleted_at')->ignore($this->route('currency'))
            ],
            'buyRate' => 'nullable|numeric',
            'saleRate' => 'nullable|numeric',
        ];
    }
}
