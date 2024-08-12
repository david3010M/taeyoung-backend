<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateQuotationRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'detail' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'currencyType' => 'nullable|string',
            'client_id' => [
                'nullable',
                Rule::exists('people', 'id')
                    ->where('type', 'client')
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
