<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentConceptRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'type' => 'required|string',
        ];
    }
}
