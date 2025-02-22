<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankAccountRequest extends FormRequest
{
    public function rules(): array
    {
        $bankAccountId = $this->route('bankaccount') ?? $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('bank_accounts')->ignore($bankAccountId)->whereNull('deleted_at'),
            ],
            'bank_id'     => 'required|exists:banks,id',
            'status'      => 'nullable|string',
            'card_number' => 'nullable|string',
            'card_type'   => 'nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
