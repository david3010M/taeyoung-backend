<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'typeDocument' => 'required|string|in:DNI,RUC',
            'dni' => [
                'nullable',
                'string',
                'min:8',
                'max:8',
                Rule::unique('people', 'dni')
                    ->where('type', 'supplier')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('supplier'))
            ],
            'ruc' => [
                'nullable',
                'string',
                'min:11',
                'max:11',
                Rule::unique('people')
                    ->where('type', 'supplier')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('supplier'))
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
