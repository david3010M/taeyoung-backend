<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="IndexWarehouseDocumentRequest",
 *   title="IndexWarehouseDocumentRequest",
 *   @OA\Property(
 *       property="date",
 *       type="array",
 *       description="Rango de fechas [inicio, fin]",
 *       @OA\Items(type="string", format="date", example="2025-02-20")
 *   ),
 *   @OA\Property(property="documentType", type="string", enum={"ingreso","salida"}, example="ingreso"),
 *   @OA\Property(property="mode", type="string", enum={"Real","Contable"}, example="Real"),
 *   @OA\Property(property="number", type="string", example="DOC-001"),
 *   @OA\Property(property="reason", type="string", example="Ajuste de stock"),
 *   @OA\Property(property="comment", type="string", example="Observaciones varias"),
 *   @OA\Property(property="sort", type="string", example="date"),
 *   @OA\Property(property="direction", type="string", enum={"asc","desc"}, example="asc"),
 *   @OA\Property(property="per_page", type="integer", example=10)
 * )
 */
class IndexWarehouseDocumentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date'         => 'nullable|array|size:2',
            'date.*'       => 'date_format:Y-m-d',
            'documentType' => 'nullable|in:ingreso,salida',
            'mode'         => 'nullable|in:Real,Contable',
            'number'       => 'nullable|string',
            'reason'       => 'nullable|string',
            'comment'      => 'nullable|string',
            'sort'         => 'nullable|string|in:date,number,id',
            'direction'    => 'nullable|string|in:asc,desc',
            'per_page'     => 'nullable|integer',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
