<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="UpdatePurchaseRequest",
 *   title="UpdatePurchaseRequest",
 *   description="Datos para actualizar una compra existente",
 *   @OA\Property(property="date", type="string", format="date", example="2025-03-01"),
 *   @OA\Property(property="number", type="string", example="COMP-0002"),
 *   @OA\Property(property="documentType", type="string", enum={"BOLETA","FACTURA"}, example="FACTURA"),
 *   @OA\Property(property="paymentType", type="string", enum={"CONTADO","CREDITO"}, example="CREDITO"),
 *   @OA\Property(property="currencyType", type="string", enum={"USD","PEN"}, example="USD"),
 *   @OA\Property(property="supplier_id", type="integer", example=2),
 *   @OA\Property(property="quotation_id", type="integer", nullable=true, example=null),
 *   @OA\Property(property="detail", type="string", example="Comentario de actualización"),
 *
 *   @OA\Property(
 *       property="detailMachinery",
 *       type="array",
 *       @OA\Items(
 *           @OA\Property(property="machinery_id", type="integer", example=5),
 *           @OA\Property(property="description", type="string", example="Nueva descripción"),
 *           @OA\Property(property="quantity", type="integer", example=3),
 *           @OA\Property(property="purchasePrice", type="number", format="float", example=6000),
 *           @OA\Property(property="realPrice", type="number", format="float", example=0),
 *           @OA\Property(property="contablePrice", type="number", format="float", example=6000)
 *       )
 *   ),
 *   @OA\Property(
 *       property="detailSpareParts",
 *       type="array",
 *       @OA\Items(
 *           @OA\Property(property="spare_part_id", type="integer", example=2),
 *           @OA\Property(property="quantity", type="integer", example=10),
 *           @OA\Property(property="purchasePrice", type="number", format="float", example=180),
 *           @OA\Property(property="realPrice", type="number", format="float", example=0),
 *           @OA\Property(property="contablePrice", type="number", format="float", example=180)
 *       )
 *   )
 * )
 */
class UpdatePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'         => 'nullable|date',
            'number'       => 'nullable|string',
            'documentType' => 'nullable|string|in:BOLETA,FACTURA',
            'paymentType'  => 'nullable|string|in:CONTADO,CREDITO',
            'currencyType' => 'nullable|string|in:USD,PEN',

            'supplier_id' => [
                'nullable',
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
