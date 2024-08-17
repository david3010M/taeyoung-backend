<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => 'nullable|string|in:DNI,RUC',
            'dni' => [
                'nullable',
                'string',
                'min:8',
                'max:8',
                Rule::unique('people')->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'ruc' => [
                'nullable',
                'string',
                'min:11',
                'max:11',
                Rule::unique('people')->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'names' => 'nullable|string',
            'fatherSurname' => 'nullable|string',
            'motherSurname' => 'nullable|string',
            'businessName' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|integer',
            'representativeDni' => 'nullable|string|min:8|max:8',
            'representativeNames' => 'nullable|string',
            'country_id' => 'nullable|integer',
        ];
    }
}
