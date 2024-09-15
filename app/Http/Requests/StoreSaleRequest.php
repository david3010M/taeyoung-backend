<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreSaleRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'documentType' => 'required|string|in:BOLETA,FACTURA',
            'paymentType' => 'required|string|in:CONTADO,CREDITO',
            'quotation_id' => [
                'nullable',
                Rule::exists('quotations', 'id'),
                Rule::unique('orders', 'quotation_id')
                    ->where('type', 'sale')
                    ->whereNull('deleted_at')
            ],
            'client_id' => [
                'required',
                Rule::exists('people', 'id')
                    ->where('type', 'client')
            ],
            'currencyType' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'quotas' => 'required|array',
            'quotas.*.days' => 'required|int',
            'quotas.*.amount' => 'required|numeric',

//            DETAILS
            'detailMachinery' => 'required_without:detailSpareParts|nullable|array',
            'detailMachinery.*.description' => 'required|string',
            'detailMachinery.*.quantity' => 'required|int',
            'detailMachinery.*.salePrice' => 'required|numeric',
            'detailSpareParts' => 'required_without:detailMachinery|nullable|array',
            'detailSpareParts.*.quantity' => 'required|numeric',
            'detailSpareParts.*.salePrice' => 'required|numeric',
            'detailSpareParts.*.spare_part_id' => 'required|exists:spare_parts,id',
        ];
    }
}
