<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="StorePurchaseRequest",
 *   title="StorePurchaseRequest",
 *   description="Datos para crear una compra",
 *   required={"date", "number", "documentType", "currencyType", "supplier_id"},
 *   @OA\Property(property="date", type="string", format="date", example="2025-02-25"),
 *   @OA\Property(property="number", type="string", example="COMP-0001"),
 *   @OA\Property(property="documentType", type="string", enum={"BOLETA","FACTURA"}, example="FACTURA"),
 *   @OA\Property(property="paymentType", type="string", enum={"CONTADO","CREDITO"}, example="CONTADO"),
 *   @OA\Property(property="currencyType", type="string", enum={"USD","PEN"}, example="PEN"),
 *   @OA\Property(property="supplier_id", type="integer", example=1),
 *   @OA\Property(property="quotation_id", type="integer", nullable=true, example=null, description="Opcional. Si no usas cotizaciones, ignora."),
 *   @OA\Property(property="detail", type="string", example="Observaciones o comentario"),
 *
 *   @OA\Property(
 *       property="detailMachinery",
 *       type="array",
 *       description="Lista de maquinarias compradas",
 *       @OA\Items(
 *           @OA\Property(property="machinery_id", type="integer", example=10),
 *           @OA\Property(property="description", type="string", example="Retroexcavadora"),
 *           @OA\Property(property="quantity", type="integer", example=2),
 *           @OA\Property(property="purchasePrice", type="number", format="float", example=5000),
 *           @OA\Property(property="realPrice", type="number", format="float", example=0, description="Si >0, afecta real_stock"),
 *           @OA\Property(property="contablePrice", type="number", format="float", example=5000, description="Si >0, afecta contable_stock")
 *       )
 *   ),
 *   @OA\Property(
 *       property="detailSpareParts",
 *       type="array",
 *       description="Lista de repuestos comprados",
 *       @OA\Items(
 *           @OA\Property(property="spare_part_id", type="integer", example=1),
 *           @OA\Property(property="quantity", type="integer", example=5),
 *           @OA\Property(property="purchasePrice", type="number", format="float", example=150),
 *           @OA\Property(property="realPrice", type="number", format="float", example=0),
 *           @OA\Property(property="contablePrice", type="number", format="float", example=150)
 *       )
 *   )
 * )
 */
class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'         => 'required|date',
            'number'       => 'required|string',
            'documentType' => ['required', Rule::in(['BOLETA','FACTURA'])],
            'paymentType'  => 'nullable|string|in:CONTADO,CREDITO',
            'currencyType' => ['required', Rule::in(['USD','PEN'])],
            'supplier_id'  => [
                'required',
                Rule::exists('people','id')
                    ->where('type','supplier')
                    ->whereNull('deleted_at')
            ],

            'quotation_id' => [
                'nullable','integer',
                Rule::exists('quotations','id')->whereNull('deleted_at')
            ],

            'detail' => 'nullable|string',

            'detailMachinery' => 'nullable|array',
            'detailMachinery.*.machinery_id'  => 'required|integer|exists:machineries,id',
            'detailMachinery.*.description'   => 'nullable|string',
            'detailMachinery.*.quantity'      => 'required|integer|min:1',
            'detailMachinery.*.purchasePrice' => 'required|numeric|min:0',
            'detailMachinery.*.realPrice'     => 'nullable|numeric|min:0',
            'detailMachinery.*.contablePrice' => 'nullable|numeric|min:0',

            'detailSpareParts' => 'nullable|array',
            'detailSpareParts.*.spare_part_id'  => 'required|integer|exists:spare_parts,id',
            'detailSpareParts.*.quantity'       => 'required|integer|min:1',
            'detailSpareParts.*.purchasePrice'  => 'required|numeric|min:0',
            'detailSpareParts.*.realPrice'      => 'nullable|numeric|min:0',
            'detailSpareParts.*.contablePrice'  => 'nullable|numeric|min:0',
        ];
    }
}
