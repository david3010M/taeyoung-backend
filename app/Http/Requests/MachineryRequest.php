<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="MachineryRequest",
 *     title="MachineryRequest",
 *     type="object",
 *     required={"name", "purchasePrice", "salePrice", "unit_id"},
 *     @OA\Property(property="code", type="string", example="MCH-001", description="Código interno de la maquinaria"),
 *     @OA\Property(property="name", type="string", example="Excavadora"),
 *     @OA\Property(property="purchasePrice", type="number", format="float", example=50000.00),
 *     @OA\Property(property="salePrice", type="number", format="float", example=65000.00),
 *     @OA\Property(property="unit_id", type="integer", example=2, description="ID de la unidad (tabla units)")
 * )
 */
class MachineryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code'          => 'nullable|string',
            'name'          => 'required|string',
            'purchasePrice' => 'required|numeric',
            'salePrice'     => 'required|numeric',
            'unit_id'       => 'required|integer|exists:units,id',
        ];
    }
}
