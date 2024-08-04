<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexDetailMachineryRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'description' => 'required|string',
            'quantity' => 'required|integer',
            'movementType' => 'required|string',
            'purchasePrice' => 'required|numeric',
            'salePrice' => 'required|numeric',
            'order_id' => 'required|integer',
            'quotation_id' => 'required|integer'
        ];
    }
}
