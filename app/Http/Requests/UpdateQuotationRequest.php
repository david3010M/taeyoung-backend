<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema (
 *     title="UpdateQuotationRequest",
 *     @OA\Property(property="date", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="detail", type="string", example="This is a detail"),
 *     @OA\Property(property="discount", type="number", example="0"),
 *     @OA\Property(property="currencyType", type="string", example="USD"),
 *     @OA\Property(property="client_id", type="integer", example="21"),
 *     @OA\Property(property="detailMachinery", type="array", @OA\Items(ref="#/components/schemas/DetailMachineryRequest")),
 *     @OA\Property(property="detailSpareParts", type="array", @OA\Items(ref="#/components/schemas/DetailSparePartRequest"))
 * )
 *
 */
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
