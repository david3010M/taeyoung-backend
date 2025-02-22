<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\UpdateRequest;

/**
 * @OA\Schema(
 *     title="UpdateQuotationRequest",
 *     description="Request body para actualizar una cotización existente",
 *     @OA\Property(property="date", type="string", format="date", example="2024-08-19"),
 *     @OA\Property(property="detail", type="string", example="This is a detail"),
 *     @OA\Property(property="discount", type="number", example="0"),
 *     @OA\Property(property="currencyType", type="string", example="USD"),
 *     @OA\Property(property="client_id", type="integer", example="21"),
 *     @OA\Property(property="igvActive", type="boolean", example=true),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"Pendiente","Finalizado"},
 *         example="Pendiente",
 *         description="Situación de la cotización"
 *     ),
 *     @OA\Property(
 *         property="detailMachinery",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/DetailMachineryUpdateRequest")
 *     ),
 *     @OA\Property(
 *         property="detailSpareParts",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/DetailSparePartRequest")
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="DetailMachineryUpdateRequest",
 *     title="DetailMachineryUpdateRequest",
 *     description="Estructura para cada detalle de maquinaria (ACTUALIZACIÓN)",
 *     required={"description", "quantity", "salePrice", "machinery_id"},
 *     @OA\Property(property="description", type="string", example="Excavadora X"),
 *     @OA\Property(property="quantity", type="integer", example=1),
 *     @OA\Property(property="purchasePrice", type="number", format="float", example=100),
 *     @OA\Property(property="salePrice", type="number", format="float", example=150),
 *     @OA\Property(property="realPrice", type="number", format="float", example=120, description="Precio real de la maquinaria"),
 *     @OA\Property(property="contablePrice", type="number", format="float", example=130, description="Precio contable de la maquinaria"),
 *     @OA\Property(property="machinery_id", type="integer", example=1, description="ID de la maquinaria (tabla machineries)")
 * )
 */
class UpdateQuotationRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'date'         => 'nullable|date',
            'detail'       => 'nullable|string',
            'discount'     => 'nullable|numeric',
            'currencyType' => 'nullable|string|in:USD,PEN',
            'client_id'    => [
                'nullable',
                Rule::exists('people', 'id')
                    ->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'igvActive'    => 'nullable|boolean',
            'status'       => 'nullable|string|in:Pendiente,Finalizado',

            // Detalles de maquinaria (renombrado el schema en la anotación)
            'detailMachinery'                  => 'nullable|array',
            'detailMachinery.*.description'    => 'required|string',
            'detailMachinery.*.quantity'       => 'required|int',
            'detailMachinery.*.purchasePrice'  => 'nullable|numeric',
            'detailMachinery.*.salePrice'      => 'required|numeric',
            'detailMachinery.*.realPrice'      => 'nullable|numeric',
            'detailMachinery.*.contablePrice'  => 'nullable|numeric',
            'detailMachinery.*.machinery_id'   => [
                'required',
                Rule::exists('machineries', 'id')
                    ->whereNull('deleted_at')
            ],

            // Detalles de repuestos (podemos reutilizar el mismo schema de Store)
            'detailSpareParts'                 => 'nullable|array',
            'detailSpareParts.*.quantity'      => 'required|numeric',
            'detailSpareParts.*.purchasePrice' => 'nullable|numeric',
            'detailSpareParts.*.salePrice'     => 'required|numeric',
            'detailSpareParts.*.realPrice'     => 'nullable|numeric',
            'detailSpareParts.*.contablePrice' => 'nullable|numeric',
            'detailSpareParts.*.spare_part_id' => [
                'required',
                Rule::exists('spare_parts', 'id')
                    ->whereNull('deleted_at')
            ],
        ];
    }
}
