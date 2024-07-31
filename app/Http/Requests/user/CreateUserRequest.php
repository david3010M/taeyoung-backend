<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'names' => 'required|string',
            'lastnames' => 'required|string',
            'username' => [
                'required',
                'string',
                Rule::unique('users', 'username')
                    ->whereNull('deleted_at'),
            ],
            'password' => 'required|string',
            'typeuser_id' => 'required|integer',
        ];
    }
}
