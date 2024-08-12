<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexUnitRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'abbreviation' => 'nullable|string',
            'sort' => 'nullable|string|in:id,name,abbreviation',
        ];
    }
}
