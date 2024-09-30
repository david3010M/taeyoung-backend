<?php

namespace App\Http\Requests;

class StoreMovementRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable|string',
            'paymentDate' => 'nullable|date',
            'typeDocument' => 'nullable|string|in:BOLETA,FACTURA',
            'cash' => 'nullable|numeric',
            'yape' => 'nullable|numeric',
            'plin' => 'nullable|numeric',
            'card' => 'nullable|numeric',
            'deposit' => 'nullable|numeric',

            'isBankPayment' => 'required|boolean',
            'numberVoucher' => 'nullable|string|required_if:isBankPayment,true',
            'voucher' => 'nullable|image|required_if:isBankPayment,true',
            'comment' => 'nullable|string',

            'bank_id' => 'nullable|integer|exists:banks,id|required_if:isBankPayment,true',
            'paymentConcept_id' => 'nullable|integer|exists:payment_concepts,id',
            'order_id' => 'nullable|integer|exists:orders,id',
            'accountReceivable_id' => 'nullable|integer|exists:account_receivables,id',
            'accountPayable_id' => 'nullable|integer|exists:account_payables,id',
        ];
    }
}
