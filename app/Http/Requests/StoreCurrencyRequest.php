<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreCurrencyRequest",
 *     @OA\Property(property="date", type="string", format="date", example="2021-01-01"),
 *     @OA\Property(property="buyRate", type="number", example="20.00"),
 *     @OA\Property(property="saleRate", type="number", example="21.00")
 * )
 */
class StoreCurrencyRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date',
                Rule::unique('currencies')->whereNull('deleted_at')
            ],
            'buyRate' => 'required|numeric',
            'saleRate' => 'required|numeric',
        ];
    }
}
