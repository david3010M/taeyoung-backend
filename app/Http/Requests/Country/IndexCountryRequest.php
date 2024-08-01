<?php

namespace App\Http\Requests\Country;

use App\Http\Requests\IndexRequest;

class IndexCountryRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            "name" => "nullable|string",
        ];
    }
}
