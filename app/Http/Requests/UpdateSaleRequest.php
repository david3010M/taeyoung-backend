<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateSaleRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'documentType' => 'nullable|string|in:BOLETA,FACTURA',
            'paymentType' => 'nullable|string|in:CONTADO,CREDITO',
            'quotation_id' => [
                'nullable',
                Rule::exists('quotations', 'id'),
                Rule::unique('orders', 'quotation_id')
                    ->where('type', 'sale')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('sale'))
            ],
            'client_id' => [
                'nullable',
                Rule::exists('people', 'id')
                    ->where('type', 'client')
            ],
            'currencyType' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'quotas' => 'nullable|array',
            'quotas.*.days' => 'nullable|int',
            'quotas.*.amount' => 'nullable|numeric',

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
