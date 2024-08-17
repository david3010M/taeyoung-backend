<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreClientRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|string|in:DNI,RUC',
            'dni' => [
                'requiredIf:type,DNI',
                'string',
                'min:8',
                'max:8',
                Rule::unique('people')->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'ruc' => [
                'requiredIf:type,RUC',
                'string',
                'min:11',
                'max:11',
                Rule::unique('people')->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'names' => 'requiredIf:type,DNI|string',
            'fatherSurname' => 'requiredIf:type,DNI|string',
            'motherSurname' => 'requiredIf:type,DNI|string',
            'businessName' => 'requiredIf:type,RUC|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|integer',
            'representativeDni' => 'nullable|string|min:8|max:8',
            'representativeNames' => 'nullable|string',
            'country_id' => 'required|integer',
        ];
    }
}
