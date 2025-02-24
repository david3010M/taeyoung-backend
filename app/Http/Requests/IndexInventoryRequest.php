<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="IndexInventoryRequest",
 *   type="object",
 *   title="IndexInventoryRequest",
 *   description="Parámetros para listar inventario (filtros y paginación)",
 *   @OA\Property(property="page", type="integer", example=1, description="Número de página"),
 *   @OA\Property(property="per_page", type="integer", example=10, description="Items por página"),
 *   @OA\Property(property="sort", type="string", example="id", description="Columna para ordenar"),
 *   @OA\Property(property="direction", type="string", enum={"asc","desc"}, example="asc", description="Dirección de orden")
 * )
 */
class IndexInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajusta según tu lógica
        return true;
    }

    public function rules(): array
    {
        return [
            'page'      => 'sometimes|integer',
            'per_page'  => 'sometimes|integer',
            'sort'      => 'sometimes|string',
            'direction' => 'sometimes|in:asc,desc',
        ];
    }
}
