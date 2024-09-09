<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSparePartRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'purchasePrice' => 'nullable|numeric',
            'salePrice' => 'nullable|numeric',
            'unit_id' => 'nullable|integer|exists:units,id',
        ];
    }
}
