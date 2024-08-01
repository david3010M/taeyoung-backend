<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexTypeUserRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
        ];
    }
}
