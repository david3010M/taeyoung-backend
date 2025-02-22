<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexBankAccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page'       => 'sometimes|integer',
            'per_page'   => 'sometimes|integer',
            'sort'       => 'sometimes|string',
            'direction'  => 'sometimes|in:asc,desc',
            'name'       => 'sometimes|string',
            'bank_id'    => 'sometimes|integer',
            'status'     => 'sometimes|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
