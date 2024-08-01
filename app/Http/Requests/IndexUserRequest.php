<?php

namespace App\Http\Requests;

class IndexUserRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'names' => 'nullable|string',
            'typeuser_id' => 'nullable|integer',
        ];
    }
}
