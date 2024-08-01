<?php

namespace App\Http\Requests\Country;

use App\Http\Requests\CreateRequest;
use Illuminate\Validation\Rule;

class CreateCountryRequest extends CreateRequest
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
