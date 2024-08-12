<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexPaymentConceptRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'number' => 'string',
            'name' => 'string',
            'type' => 'string',
        ];
    }
}
