<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\StoreRequest;

/**
 * @OA\Schema(
 *     schema="StoreQuotationRequest",
 *     title="StoreQuotationRequest",
 *     description="Request body para crear una nueva cotización",
 *     required={
 *       "date",
 *       "currencyType",
 *       "client_id",
 *       "igvActive",
 *       "detailMachinery",
 *       "detailSpareParts"
 *     },
 *     @OA\Property(property="date", type="string", format="date", example="2024-08-19", description="Fecha de la cotización"),
 *     @OA\Property(property="detail", type="string", example="Detalle adicional de la cotización"),
 *     @OA\Property(property="discount", type="number", format="float", example=0, description="Descuento aplicado"),
 *     @OA\Property(
 *         property="currencyType",
 *         type="string",
 *         enum={"USD", "PEN"},
 *         example="USD",
 *         description="Tipo de moneda (USD o PEN)"
 *     ),
 *     @OA\Property(
 *         property="client_id",
 *         type="integer",
 *         example=21,
 *         description="ID del cliente (tabla people con type=client)"
 *     ),
 *     @OA\Property(
 *         property="igvActive",
 *         type="boolean",
 *         example=true,
 *         description="Indica si se aplica IGV (true) o no (false)"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"Pendiente", "Finalizado"},
 *         example="Pendiente",
 *         description="Situación de la cotización"
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         description="Arreglo de archivos (imágenes) adjuntos",
 *         @OA\Items(type="file", format="binary")
 *     ),
 *     @OA\Property(
 *         property="detailMachinery",
 *         type="array",
 *         description="Listado de detalles de maquinaria",
 *         @OA\Items(ref="#/components/schemas/DetailMachineryRequest")
 *     ),
 *     @OA\Property(
 *         property="detailSpareParts",
 *         type="array",
 *         description="Listado de detalles de repuestos",
 *         @OA\Items(ref="#/components/schemas/DetailSparePartRequest")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="DetailMachineryRequest",
 *     title="DetailMachineryRequest",
 *     description="Estructura para cada detalle de maquinaria (CREACIÓN)",
 *     required={"description", "quantity", "salePrice", "machinery_id"},
 *     @OA\Property(property="description", type="string", example="Descripción de la maquinaria"),
 *     @OA\Property(property="quantity", type="integer", example=1),
 *     @OA\Property(property="purchasePrice", type="number", format="float", example=100),
 *     @OA\Property(property="salePrice", type="number", format="float", example=150),
 *     @OA\Property(property="realPrice", type="number", format="float", example=120, description="Precio real de la maquinaria"),
 *     @OA\Property(property="contablePrice", type="number", format="float", example=130, description="Precio contable de la maquinaria"),
 *     @OA\Property(property="machinery_id", type="integer", example=1, description="ID de la maquinaria (tabla machineries)")
 * )
 *
 * @OA\Schema(
 *     schema="DetailSparePartRequest",
 *     title="DetailSparePartRequest",
 *     description="Estructura para cada detalle de repuesto (CREACIÓN)",
 *     required={"quantity", "salePrice", "spare_part_id"},
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="purchasePrice", type="number", format="float", example=80),
 *     @OA\Property(property="salePrice", type="number", format="float", example=100),
 *     @OA\Property(property="realPrice", type="number", format="float", example=90, description="Precio real del repuesto"),
 *     @OA\Property(property="contablePrice", type="number", format="float", example=95, description="Precio contable del repuesto"),
 *     @OA\Property(property="spare_part_id", type="integer", example=1, description="ID del repuesto (tabla spare_parts)")
 * )
 */
class StoreQuotationRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'detail' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'currencyType' => 'required|string|in:USD,PEN',
            'client_id' => [
                'required',
                Rule::exists('people', 'id')
                    ->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'igvActive' => 'required|boolean',

            // Campo status: Pendiente o Finalizado
            'status' => 'nullable|string|in:Pendiente,Finalizado',

            'images' => 'nullable|array',
            'images.*' => 'required|file',

            // DETALLES DE MAQUINARIA
            'detailMachinery' => 'required_without:detailSpareParts|nullable|array',
            'detailMachinery.*.description' => 'required|string',
            'detailMachinery.*.quantity' => 'required|int',
            'detailMachinery.*.purchasePrice' => 'nullable|numeric',
            'detailMachinery.*.salePrice' => 'required|numeric',
            'detailMachinery.*.realPrice' => 'nullable|numeric',
            'detailMachinery.*.contablePrice' => 'nullable|numeric',
            'detailMachinery.*.machinery_id' => [
                'required',
                Rule::exists('machineries', 'id')
                    ->whereNull('deleted_at')
            ],

            // DETALLES DE REPUESTOS
            'detailSpareParts' => 'required_without:detailMachinery|nullable|array',
            'detailSpareParts.*.quantity' => 'required|numeric',
            'detailSpareParts.*.purchasePrice' => 'nullable|numeric',
            'detailSpareParts.*.salePrice' => 'required|numeric',
            'detailSpareParts.*.realPrice' => 'nullable|numeric',
            'detailSpareParts.*.contablePrice' => 'nullable|numeric',
            'detailSpareParts.*.spare_part_id' => [
                'required',
                Rule::exists('spare_parts', 'id')
                    ->whereNull('deleted_at')
            ],
        ];
    }
}
