<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     title="StoreSaleRequest",
 *     type="object",
 *     required={"date", "documentType", "paymentType", "quotas", "client_id", "detailMachinery|detailSpareParts"},
 *     @OA\Property(property="date", type="string", format="date", example="2024-08-04"),
 *     @OA\Property(property="documentType", type="string", enum={"BOLETA", "FACTURA"}, example="BOLETA"),
 *     @OA\Property(property="paymentType", type="string", enum={"CONTADO", "CREDITO"}, example="CREDITO"),
 *     @OA\Property(property="quotation_id", type="integer", example="21"),
 *     @OA\Property(property="client_id", type="integer", example="21"),
 *     @OA\Property(property="currencyType", type="string", example="PEN"),
 *     @OA\Property(property="discount", type="number", example="100"),
 *     @OA\Property(property="igvActive", type="boolean", example="true", enum={"true", "false"}),
 *     @OA\Property(property="quotas", type="array", @OA\Items(
 *         @OA\Property(property="days", type="integer", example="7"),
 *         @OA\Property(property="amount", type="number", example="14378.6"),
 *     )),
 *     @OA\Property(property="detailMachinery[]", type="array", @OA\Items(
 *         @OA\Property(property="description", type="string", example="Machinery description"),
 *         @OA\Property(property="quantity", type="integer", example="2"),
 *         @OA\Property(property="salePrice", type="number", example="4000"),
 *     )),
 *     @OA\Property(property="detailSpareParts[]", type="array", @OA\Items(
 *         @OA\Property(property="quantity", type="integer", example="4"),
 *         @OA\Property(property="salePrice", type="number", example="767.5"),
 *         @OA\Property(property="spare_part_id", type="integer", example="1"),
 *     )),
 * )
 *
 */
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
                Rule::exists('quotations', 'id')
                    ->whereNull('deleted_at'),
                Rule::unique('orders', 'quotation_id')
                    ->where('type', 'sale')
                    ->whereNull('deleted_at')
            ],
            'client_id' => [
                'required',
                Rule::exists('people', 'id')
                    ->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'igvActive' => 'required|boolean',
            'currencyType' => 'required|string|in:USD,PEN',
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
            'detailSpareParts.*.spare_part_id' => [
                'required',
                Rule::exists('spare_parts', 'id')
                    ->whereNull('deleted_at')
            ]
        ];
    }
}
