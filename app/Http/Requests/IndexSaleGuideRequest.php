<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="IndexSaleGuideFilters",
 *   title="IndexSaleGuideFilters",
 *   description="Parámetros de filtrado para listar Guías de Venta",
 *   @OA\Property(property="number", type="string", example="GUIDE-001"),
 *   @OA\Property(
 *       property="date",
 *       type="array",
 *       @OA\Items(type="string", format="date", example="2025-02-20"),
 *       description="Rango de fechas [inicio, fin]"
 *   ),
 *   @OA\Property(property="client_id", type="integer", example=5, description="ID del cliente"),
 *   @OA\Property(property="client_filterName", type="string", example="Empresa ABC", description="Filtrar por nombre de cliente"),
 *   @OA\Property(property="client_country_id", type="integer", example=2, description="Filtrar por país del cliente"),
 *   @OA\Property(property="page", type="integer", example=1, description="Número de página"),
 *   @OA\Property(property="per_page", type="integer", example=15, description="Cantidad de elementos por página")
 * )
 */
class IndexSaleGuideRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number'               => 'nullable|string', // Filtra por número
            'date'                 => 'nullable|array|size:2', // Rango de fechas
            'date.0'               => 'nullable|date_format:Y-m-d',
            'date.1'               => 'nullable|date_format:Y-m-d',
            'client_id'            => 'nullable|integer', // Filtra por cliente ID
            'client_filterName'    => 'nullable|string|max:255', // Filtra por nombre del cliente
            'client_country_id'    => 'nullable|integer', // Filtra por país del cliente
            'page'                 => 'nullable|integer',
            'per_page'             => 'nullable|integer',
        ];
    }
}
