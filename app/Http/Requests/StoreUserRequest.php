<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreUserRequest",
 *     title="Create User Request",
 *     required={"names", "lastnames", "username", "password", "typeuser_id"},
 *     @OA\Property(property="names", type="string", example="John"),
 *     @OA\Property(property="lastnames", type="string", example="Doe"),
 *     @OA\Property(property="username", type="string", example="johndoe"),
 *     @OA\Property(property="password", type="string", example="password"),
 *     @OA\Property(property="typeuser_id", type="integer", example="1")
 * )
 */
class StoreUserRequest extends StoreRequest
{
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
