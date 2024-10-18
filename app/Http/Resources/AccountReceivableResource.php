<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AccountReceivableResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="paymentType", type="string", example="Contado"),
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
 *     @OA\Property(property="quotation", type="string", example="COTI-123456"),
 *     @OA\Property(property="order", type="string", example="VENT-123456"),
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
    protected bool $includeReceivableField = false;
    protected bool $includePayableField = false;

    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'paymentType' => $this->paymentType,
            'days' => $this->days,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'paymentAmount' => round($this->amount - $this->balance, 2),
            'status' => $this->status,
            'client_id' => $this->client_id,
            'client' => $this->client->filterName,
            'country' => $this->client->country->name,
            'currency_id' => $this->currency_id,
            'order_id' => $this->order_id,
            'quotation' => "COTI-" . $this->order->quotation->number,
            'order' => "VENT-" . $this->order->number,
        ];

        if ($this->includeReceivableField) {
            $data['extensions'] = $this->extensions;
            $data['movements'] = MovementResource::collection($this->movements)
                ->map(fn($movement) => $movement->withReceivable());
        }

        if ($this->includePayableField) {
            $data['extensions'] = $this->extensions;
            $data['movements'] = MovementResource::collection($this->movements)
                ->map(fn($movement) => $movement->withPayable());
        }


        return $data;
    }

    public function withReceivable(): self
    {
        $this->includeReceivableField = true;
        return $this;
    }

    public function withPayable(): self
    {
        $this->includePayableField = true;
        return $this;
    }
}
