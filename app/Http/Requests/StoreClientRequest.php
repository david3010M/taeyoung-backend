<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreClientRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'typeDocument' => 'required|string|in:DNI,RUC',
            'dni' => [
                'requiredIf:typeDocument,DNI',
                'string',
                'min:8',
                'max:8',
                Rule::unique('people', 'dni')
                    ->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'ruc' => [
                'requiredIf:typeDocument,RUC',
                'string',
                'min:11',
                'max:11',
                Rule::unique('people', 'ruc')->whereNull('deleted_at')
            ],
            'names' => 'requiredIf:typeDocument,DNI|string',
            'fatherSurname' => 'requiredIf:typeDocument,DNI|string',
            'motherSurname' => 'requiredIf:typeDocument,DNI|string',
            'businessName' => 'requiredIf:typeDocument,RUC|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|integer',
            'representativeDni' => 'nullable|string|min:8|max:8',
            'representativeNames' => 'nullable|string',
            'country_id' => 'required|integer',
        ];
    }
}
