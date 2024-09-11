<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema (
 *     schema="StorePurchaseRequest",
 *     required={"date", "detailMachinery", "supplier_id"},
 *     @OA\Property(property="quotation_id", type="integer", example="21"),
 *     @OA\Property(property="date", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="detail", type="string", example="This is a detail"),
 *     @OA\Property(property="currencyType", type="string", example="USD"),
 *     @OA\Property(property="supplier_id", type="integer", example="1"),
 *     @OA\Property(property="detailMachinery", type="array", @OA\Items(
 *         @OA\Property(property="description", type="string", example="This is a description"),
 *         @OA\Property(property="quantity", type="integer", example="1"),
 *         @OA\Property(property="purchasePrice", type="number", example="100"),
 *     )),
 *     @OA\Property(property="detailSpareParts", type="array", @OA\Items(
 *         @OA\Property(property="quantity", type="integer", example="2"),
 *         @OA\Property(property="spare_part_id", type="integer", example="1"),
 *         @OA\Property(property="purchasePrice", type="number", example="100"),
 *     )),
 * )
 */
class StorePurchaseRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'quotation_id' => [
                'nullable',
                Rule::exists('quotations', 'id'),
                Rule::unique('orders', 'quotation_id')
                    ->where('type', 'purchase')
                    ->whereNull('deleted_at')
            ],

            'documentType' => 'nullable|string', // BOLETA, FACTURA, TICKET
            'number' => 'required|string',
            'date' => 'required|date',
            'detail' => 'nullable|string',

            'currencyType' => 'nullable|string',
            'supplier_id' => [
                'required',
                Rule::exists('people', 'id')
                    ->where('type', 'supplier')
            ],

            'detailMachinery' => 'required_without:detailSpareParts|nullable|array',
            'detailMachinery.*.description' => 'required|string',
            'detailMachinery.*.quantity' => 'required|int',
            'detailMachinery.*.purchasePrice' => 'required|numeric',
            'detailSpareParts' => 'required_without:detailMachinery|nullable|array',
            'detailSpareParts.*.quantity' => 'required|numeric',
            'detailSpareParts.*.purchasePrice' => 'required|numeric',
            'detailSpareParts.*.spare_part_id' => 'required|exists:spare_parts,id',
        ];
    }
}
