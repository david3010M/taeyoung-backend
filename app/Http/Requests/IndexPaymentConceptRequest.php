<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexPaymentConceptRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable|string',
            'name' => 'nullable|string',
            'type' => 'nullable|string',
        ];
    }
}
