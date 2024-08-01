<?php

namespace App\Http\Requests\User;

use App\Http\Requests\IndexRequest;

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
