<?php

namespace App\Http\Requests;

class IndexAccountPayableRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'date' => 'nullable|array|size:2',
            'date.0' => 'nullable|date_format:Y-m-d',
            'date.1' => 'nullable|date_format:Y-m-d',
        ];
    }
}
