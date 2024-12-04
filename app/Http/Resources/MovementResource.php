<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementResource extends JsonResource
{
    protected bool $includeReceivableField = false;
    protected bool $includePayableField = false;

    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'currencyType' => $this->currencyType,
            'number' => "PAGO-" . $this->number,
            'paymentDate' => $this->paymentDate ? $this->paymentDate->format('Y-m-d') : null,
            'total' => $this->total,
            'cash' => $this->cash,
            'yape' => $this->yape,
            'plin' => $this->plin,
            'card' => $this->card,
            'deposit' => $this->deposit,
            'isBankPayment' => $this->isBankPayment,
            'comment' => $this->comment,
            'status' => $this->status,
            'bank_id' => $this->bank_id,
            'currency' => $this->currency,
        ];

        if ($this->includeReceivableField) {
            $data['accountReceivable_id'] = $this->accountReceivable_id;
        }

        if ($this->includePayableField) {
            $data['accountPayable_id'] = $this->accountPayable_id;
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
