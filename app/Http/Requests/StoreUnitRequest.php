<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreUnitRequest",
 *     title="StoreUnitRequest",
 *     required={"name", "abbreviation"},
 *     @OA\Property(property="name", type="string", example="Unidad"),
 *     @OA\Property(property="abbreviation", type="string", example="UN")
 * )
 */
class StoreUnitRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('units', 'name')->whereNull('deleted_at'),
            ],
            'abbreviation' => 'required|string',
        ];
    }
}
