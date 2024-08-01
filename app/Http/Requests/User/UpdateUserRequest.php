<?php

namespace App\Http\Requests\User;

use App\Http\Requests\UpdateRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends UpdateRequest
{
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
            'password' => 'nullable|string',
            'typeuser_id' => 'nullable|integer|exists:type_users,id',
        ];
    }
}
