<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexClientRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'dni' => 'nullable|integer',
            'ruc' => 'nullable|integer',
            'filterName' => 'nullable|string',
            'email' => 'nullable|string',
            'phone' => 'nullable|integer',
            'country_id' => 'nullable|string',
        ];
    }
}
