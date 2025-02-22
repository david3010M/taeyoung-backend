<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="BankAccountResource",
 *     type="object",
 *     title="BankAccountResource",
 *     required={"id", "name", "bank_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Cuenta Corriente 01"),
 *     @OA\Property(property="bank_id", type="integer", example=2),
 *     @OA\Property(
 *         property="bank",
 *         ref="#/components/schemas/BankResource",
 *         description="Banco al que pertenece la cuenta"
 *     ),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="card_number", type="string", example="1234-5678-9012-3456"),
 *     @OA\Property(property="card_type", type="string", example="VISA"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="BankAccountCollection",
 *     type="object",
 *     title="BankAccountCollection",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/BankAccountResource")
 *     ),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema(
 *     schema="BankAccountRequest",
 *     type="object",
 *     title="BankAccountRequest",
 *     required={"name", "bank_id"},
 *     @OA\Property(property="name", type="string", example="Cuenta Corriente 01"),
 *     @OA\Property(property="bank_id", type="integer", example=2),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="card_number", type="string", example="1234-5678-9012-3456"),
 *     @OA\Property(property="card_type", type="string", example="VISA")
 * )
 */
class BankAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'bank_id'     => $this->bank_id,
            // Aquí anidamos el banco usando BankResource
            'bank'        => new BankResource($this->bank),
            'status'      => $this->status,
            'card_number' => $this->card_number,
            'card_type'   => $this->card_type,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
