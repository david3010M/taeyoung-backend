<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateQuotationRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'detail' => 'nullable|string',
            'currency_id' => 'nullable|exists:currencies,id',

            'date' => 'nullable|date',
            'currencyType' => 'nullable|string',
            'price' => 'nullable|numeric',
            'initialPayment' => 'nullable|numeric',
            'debts' => 'nullable|int',
            'client_id' => [
                'nullable',
                Rule::exists('people', 'id')->where('type', 'client')
            ],
            'detailMachinery' => 'nullable|array',
            'detailMachinery.*.description' => 'required|string',
            'detailMachinery.*.quantity' => 'required|int',
            'detailMachinery.*.salePrice' => 'required|numeric',
            'detailSpareParts' => 'nullable|array',
            'detailSpareParts.*.quantity' => 'required|numeric',
            'detailSpareParts.*.spare_part_id' => 'required|exists:spare_parts,id',
        ];
    }
}
