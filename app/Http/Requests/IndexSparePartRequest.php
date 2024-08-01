<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexSparePartRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'code' => 'nullable|string',
            'name' => 'nullable|string',
        ];
    }
}
