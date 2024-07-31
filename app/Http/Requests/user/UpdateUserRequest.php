<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'names' => 'nullable|string',
            'lastnames' => 'nullable|string',
            'username' => [
                'nullable',
                'string',
                Rule::unique('users', 'username')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('user')),
            ],
            'password' => 'required|string',
            'typeuser_id' => 'nullable|integer|exists:type_users,id',
        ];
    }
}
