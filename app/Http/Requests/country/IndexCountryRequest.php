<?php

namespace App\Http\Requests\country;

use App\Http\Requests\IndexRequest;
use Illuminate\Foundation\Http\FormRequest;

class IndexCountryRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            "name" => "nullable|string",
        ];
    }
}
