<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     title="UpdateSaleRequest",
 *     type="object",
 *     @OA\Property(property="date", type="string", format="date", example="2024-08-04"),
 *     @OA\Property(property="documentType", type="string", enum={"BOLETA","FACTURA"}, example="BOLETA"),
 *     @OA\Property(property="paymentType", type="string", enum={"CONTADO","CREDITO"}, example="CREDITO"),
 *     @OA\Property(property="quotation_id", type="integer", example=21),
 *     @OA\Property(property="client_id", type="integer", example=21),
 *     @OA\Property(property="currencyType", type="string", example="PEN"),
 *     @OA\Property(property="discount", type="number", example=100),
 *     @OA\Property(property="igvActive", type="boolean", example=true),
 *     @OA\Property(
 *         property="quotas",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="days", type="integer", example=7),
 *             @OA\Property(property="amount", type="number", example=14378.6)
 *         )
 *     ),
 *     @OA\Property(
 *         property="detailMachinery",
 *         type="array",
 *         description="Lista de maquinarias vendidas",
 *         @OA\Items(
 *             @OA\Property(property="machinery_id", type="integer", example=5),
 *             @OA\Property(property="quantity", type="integer", example=2),
 *             @OA\Property(property="salePrice", type="number", example=4000),
 *             @OA\Property(property="realPrice", type="number", example=3800, description="Precio real de la maquinaria"),
 *             @OA\Property(property="contablePrice", type="number", example=3900, description="Precio contable de la maquinaria")
 *         )
 *     ),
 *     @OA\Property(
 *         property="detailSpareParts",
 *         type="array",
 *         description="Lista de repuestos vendidos",
 *         @OA\Items(
 *             @OA\Property(property="quantity", type="integer", example=4),
 *             @OA\Property(property="salePrice", type="number", example=767.5),
 *             @OA\Property(property="realPrice", type="number", example=750, description="Precio real del repuesto"),
 *             @OA\Property(property="contablePrice", type="number", example=760, description="Precio contable del repuesto"),
 *             @OA\Property(property="spare_part_id", type="integer", example=1)
 *         )
 *     )
 * )
 */
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
                Rule::exists('quotations', 'id')
                    ->whereNull('deleted_at'),
                Rule::unique('orders', 'quotation_id')
                    ->where('type', 'sale')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('sale'))
            ],
            'client_id' => [
                'nullable',
                Rule::exists('people', 'id')
                    ->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'igvActive'    => 'required|boolean',
            'currencyType' => 'nullable|string|in:USD,PEN',
            'discount'     => 'nullable|numeric',

            'quotas'               => 'required|array',
            'quotas.*.days'        => 'required|int',
            'quotas.*.amount'      => 'required|numeric',

            'detailMachinery'                      => 'required_without:detailSpareParts|nullable|array',
            'detailMachinery.*.machinery_id'       => [
                'required',
                'integer',
                Rule::exists('machineries', 'id')
                    ->whereNull('deleted_at')
            ],
            'detailMachinery.*.quantity'           => 'required|int',
            'detailMachinery.*.salePrice'          => 'required|numeric',
            'detailMachinery.*.realPrice'          => 'nullable|numeric',
            'detailMachinery.*.contablePrice'      => 'nullable|numeric',

            'detailSpareParts'                     => 'required_without:detailMachinery|nullable|array',
            'detailSpareParts.*.quantity'          => 'required|numeric',
            'detailSpareParts.*.salePrice'         => 'required|numeric',
            'detailSpareParts.*.realPrice'         => 'nullable|numeric',
            'detailSpareParts.*.contablePrice'     => 'nullable|numeric',
            'detailSpareParts.*.spare_part_id'     => [
                'required',
                Rule::exists('spare_parts', 'id')
                    ->whereNull('deleted_at')
            ],
        ];
    }
}
