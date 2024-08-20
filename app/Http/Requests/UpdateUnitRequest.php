<?php

namespace App\Http\Requests;


use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateUnitRequest",
 *     title="UpdateUnitRequest",
 *     @OA\Property(property="name", type="string", example="Unidad"),
 *     @OA\Property(property="abbreviation", type="string", example="UN")
 * )
 */
class UpdateUnitRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'nullable',
                'string',
                Rule::unique('units', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($this->unit),
            ],
            'abbreviation' => 'nullable|string',
        ];
    }
}
