<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreUnitRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('units', 'name')->whereNull('deleted_at'),
            ],
            'abbreviation' => 'required|string',
        ];
    }
}
