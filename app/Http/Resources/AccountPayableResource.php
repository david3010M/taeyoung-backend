<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AccountPayableResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="days", type="integer", example="30"),
 *     @OA\Property(property="date", type="string", format="date", example="2021-01-01"),
 *     @OA\Property(property="amount", type="number", example="1000.00"),
 *     @OA\Property(property="balance", type="number", example="1000.00"),
 *     @OA\Property(property="status", type="string", example="Pending"),
 *     @OA\Property(property="supplier_id", type="integer", example="1"),
 *     @OA\Property(property="supplier", type="string", example="John Doe"),
 *     @OA\Property(property="country", type="string", example="United States"),
 *     @OA\Property(property="currency_id", type="integer", example="1"),
 *     @OA\Property(property="order_id", type="integer", example="1")
 * )
 *
 * @OA\Schema (
 *     schema="AccountPayableCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AccountPayableResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 */
class AccountPayableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'days' => $this->days,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'status' => $this->status,
            'supplier_id' => $this->supplier_id,
            'supplier' => $this->supplier->filterName,
            'country' => $this->supplier->country->name,
            'currency_id' => $this->currency_id,
            'order_id' => $this->order_id,
        ];
    }
}
