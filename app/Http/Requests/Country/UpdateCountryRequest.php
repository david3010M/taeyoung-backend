<?php

namespace App\Http\Requests\Country;

use App\Http\Requests\UpdateRequest;
use Illuminate\Validation\Rule;

class UpdateCountryRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            "name" => [
                "required",
                "string",
                Rule::unique('countries', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('country')),
            ]
        ];
    }
}
