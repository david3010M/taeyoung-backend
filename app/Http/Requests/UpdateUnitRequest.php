<?php

namespace App\Http\Requests;


class UpdateUnitRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'abbreviation' => 'nullable|string',
        ];
    }
}
