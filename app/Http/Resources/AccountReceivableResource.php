<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AccountReceivableResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="days", type="integer", example="30"),
 *     @OA\Property(property="date", type="string", format="date", example="2021-01-01"),
 *     @OA\Property(property="amount", type="number", example="1000.00"),
 *     @OA\Property(property="balance", type="number", example="1000.00"),
 *     @OA\Property(property="status", type="string", example="Pending"),
 *     @OA\Property(property="client_id", type="integer", example="1"),
 *     @OA\Property(property="client", type="string", example="John Doe"),
 *     @OA\Property(property="country", type="string", example="United States"),
 *     @OA\Property(property="currency_id", type="integer", example="1"),
 *     @OA\Property(property="order_id", type="integer", example="1"),
 *     @OA\Property(property="order", type="string", example="123456"),
 * )
 *
 * @OA\Schema (
 *     schema="AccountReceivableCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AccountReceivableResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 *
 */
class AccountReceivableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'paymentType' => $this->paymentType,
            'days' => $this->days,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'paymentAmount' => $this->amount - $this->balance,
            'status' => $this->status,
            'client_id' => $this->client_id,
            'client' => $this->client->filterName,
            'country' => $this->client->country->name,
            'currency_id' => $this->currency_id,
            'order_id' => $this->order_id,
            'order' => $this->order->number,
        ];
    }
}
