<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="StorePayableMovementRequest",
 *     title="StorePayableMovementRequest",
 *     description="Store Payable Movement Request",
 *     type="object",
 *     @OA\Property( property="paymentDate", type="date", example="2021-10-13"),
 *     @OA\Property( property="cash", type="numeric", example="100.00"),
 *     @OA\Property( property="yape", type="numeric", example="100.00"),
 *     @OA\Property( property="plin", type="numeric", example="100.00"),
 *     @OA\Property( property="card", type="numeric", example="100.00"),
 *     @OA\Property( property="deposit", type="numeric", example="100.00"),
 *     @OA\Property( property="isBankPayment", type="boolean", example="true"),
 *     @OA\Property( property="comment", type="string", example="Comment"),
 *     @OA\Property( property="bank_id", type="integer", example="1"),
 *     @OA\Property( property="accountPayable_id", type="integer", example="1"),
 * )
 *
 * @OA\Schema(
 *      schema="StoreReceivableMovementRequest",
 *      title="StoreReceivableMovementRequest",
 *      description="Store Receivable Movement Request",
 *      type="object",
 *      @OA\Property( property="paymentDate", type="date", example="2021-10-13"),
 *      @OA\Property( property="cash", type="numeric", example="100.00"),
 *      @OA\Property( property="yape", type="numeric", example="100.00"),
 *      @OA\Property( property="plin", type="numeric", example="100.00"),
 *      @OA\Property( property="card", type="numeric", example="100.00"),
 *      @OA\Property( property="deposit", type="numeric", example="100.00"),
 *      @OA\Property( property="isBankPayment", type="boolean", example="true"),
 *      @OA\Property( property="comment", type="string", example="Comment"),
 *      @OA\Property( property="bank_id", type="integer", example="1"),
 *      @OA\Property( property="accountReceivable_id", type="integer", example="1"),
 *  )
 */
class StoreMovementRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable|string',
            'paymentDate' => 'nullable|date',
            'cash' => 'nullable|numeric',
            'yape' => 'nullable|numeric',
            'plin' => 'nullable|numeric',
            'card' => 'nullable|numeric',
            'deposit' => 'nullable|numeric',

            'isBankPayment' => 'required|boolean',
            'comment' => 'nullable|string',

            'bank_id' => 'nullable|integer|exists:banks,id|required_if:isBankPayment,true',
        ];
    }
}
