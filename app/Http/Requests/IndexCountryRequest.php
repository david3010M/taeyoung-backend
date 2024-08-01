<?php

namespace App\Http\Requests;

class IndexCountryRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            "name" => "nullable|string",
        ];
    }
}
