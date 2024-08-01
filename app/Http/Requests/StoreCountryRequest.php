<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreCountryRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            "name" => [
                "required",
                "string",
                Rule::unique('countries', 'name')
                    ->whereNull('deleted_at')
            ],
        ];
    }
}
