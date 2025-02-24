<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="IndexPurchaseRequest",
 *   title="IndexPurchaseRequest",
 *   description="Parámetros de filtrado para listar compras",
 *   @OA\Property(property="documentType", type="string", enum={"BOLETA","FACTURA"}, example="BOLETA"),
 *   @OA\Property(property="number", type="string", example="COMP-0001"),
 *   @OA\Property(
 *       property="date",
 *       type="array",
 *       @OA\Items(type="string", format="date", example="2025-02-20"),
 *       description="Rango de fechas [inicio, fin]"
 *   ),
 *   @OA\Property(property="supplier_id", type="integer", example=1),
 *   @OA\Property(property="supplier_name", type="string", example="Distribuidora ABC"),
 *   @OA\Property(property="page", type="integer", example=1),
 *   @OA\Property(property="per_page", type="integer", example=15)
 * )
 */
class IndexPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documentType'  => 'nullable|string|in:BOLETA,FACTURA',
            'number'        => 'nullable|string',
            'date'          => 'nullable|array|size:2',
            'date.0'        => 'nullable|date_format:Y-m-d',
            'date.1'        => 'nullable|date_format:Y-m-d',
            'supplier_id'   => 'nullable|integer',
            'supplier_name' => 'nullable|string|max:255', // Nueva regla para filtrar por nombre del proveedor
            'page'          => 'nullable|integer',
            'per_page'      => 'nullable|integer',
        ];
    }
}
