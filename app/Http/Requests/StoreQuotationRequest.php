<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreQuotationRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'detail' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'currencyType' => 'nullable|string',
            'client_id' => [
                'required',
                Rule::exists('people', 'id')
                    ->where('type', 'client')
            ],

//            DETAILS
            'detailMachinery' => 'required_without:detailSpareParts|nullable|array',
            'detailMachinery.*.description' => 'required|string',
            'detailMachinery.*.quantity' => 'required|int',
            'detailMachinery.*.salePrice' => 'required|numeric',
            'detailSpareParts' => 'required_without:detailMachinery|nullable|array',
            'detailSpareParts.*.quantity' => 'required|numeric',
            'detailSpareParts.*.spare_part_id' => 'required|exists:spare_parts,id',
        ];
    }
}
