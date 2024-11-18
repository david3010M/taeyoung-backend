<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema (
 *     title="StoreQuotationRequest",
 *     required={"date", "detail", "discount", "currencyType", "client_id", "detailMachinery", "detailSpareParts"},
 *     @OA\Property(property="date", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="detail", type="string", example="This is a detail"),
 *     @OA\Property(property="discount", type="number", example="0"),
 *     @OA\Property(property="currencyType", type="string", example="USD"),
 *     @OA\Property(property="client_id", type="integer", example="21"),
 *     @OA\Property(property="igvActive", type="string", example="true"),
 *     @OA\Property(property="images[]", type="array", @OA\Items(type="file", format="binary")),
 *     @OA\Property(property="detailMachinery[]", type="array", @OA\Items(ref="#/components/schemas/DetailMachineryRequest")),
 *     @OA\Property(property="detailSpareParts[]", type="array", @OA\Items(ref="#/components/schemas/DetailSparePartRequest"))
 * )
 *
 * @OA\Schema (
 *     schema="DetailMachineryRequest",
 *     required={"description", "quantity", "salePrice"},
 *     @OA\Property(property="description", type="string", example="Description"),
 *     @OA\Property(property="quantity", type="integer", example="1"),
 *     @OA\Property(property="purchasePrice", type="number", example="100"),
 *     @OA\Property(property="salePrice", type="number", example="100")
 * )
 *
 * @OA\Schema (
 *     schema="DetailSparePartRequest",
 *     required={"quantity", "spare_part_id"},
 *     @OA\Property(property="quantity", type="integer", example="1"),
 *     @OA\Property(property="spare_part_id", type="integer", example="1"),
 *     @OA\Property(property="purchasePrice", type="number", example="100"),
 *     @OA\Property(property="salePrice", type="number", example="100")
 * )
 *
 */
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
                    ->whereNull('deleted_at')
            ],

            'igvActive' => 'required|boolean',
            'images' => 'nullable|array',
            'images.*' => 'required|file',

//            DETAILS
            'detailMachinery' => 'required_without:detailSpareParts|nullable|array',
            'detailMachinery.*.description' => 'required|string',
            'detailMachinery.*.quantity' => 'required|int',
            'detailMachinery.*.purchasePrice' => 'nullable|numeric',
            'detailMachinery.*.salePrice' => 'required|numeric',
            'detailSpareParts' => 'required_without:detailMachinery|nullable|array',
            'detailSpareParts.*.quantity' => 'required|numeric',
            'detailSpareParts.*.purchasePrice' => 'nullable|numeric',
            'detailSpareParts.*.salePrice' => 'required|numeric',
            'detailSpareParts.*.spare_part_id' => [
                'required',
                Rule::exists('spare_parts', 'id')
                    ->whereNull('deleted_at')
            ]
        ];
    }
}
