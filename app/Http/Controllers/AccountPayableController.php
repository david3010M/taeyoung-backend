<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexAccountPayableRequest;
use App\Http\Requests\StoreMovementRequest;
use App\Http\Resources\AccountPayableResource;
use App\Models\AccountPayable;
use App\Http\Requests\StoreAccountPayableRequest;
use App\Http\Requests\UpdateAccountPayableRequest;
use App\Models\Currency;
use App\Models\Movement;

class AccountPayableController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/accountPayable",
     *     tags={"Account Payables"},
     *     summary="Get all account payables",
     *     description="Get all account payables",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="date", name="date[]", in="query", required=false, description="Account Payable Date", @OA\Schema(type="array", @OA\Items(type="string", format="date"))),
     *     @OA\Parameter( name="amount", in="query", description="Filter by amount", @OA\Schema(type="string")),
     *     @OA\Parameter( name="balance", in="query", description="Filter by balance", @OA\Schema(type="string")),
     *     @OA\Parameter( name="status", in="query", description="Filter by status", @OA\Schema(type="string")),
     *     @OA\Parameter( name="supplier_id", in="query", description="Filter by client_id", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="supplier$filterName", in="query", description="Filter by supplier$filterName", @OA\Schema(type="string")),
     *     @OA\Parameter( name="supplier$country_id", in="query", description="Filter by supplier$country_id", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="order_id", in="query", description="Filter by order_id", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="currency$date", in="query", description="Filter by currency$date", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/AccountPayableResource")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function index(IndexAccountPayableRequest $request)
    {
        return $this->getFilteredResults(
            AccountPayable::class,
            $request,
            AccountPayable::filters,
            AccountPayable::sorts,
            AccountPayableResource::class
        );
    }

    public function store(StoreAccountPayableRequest $request)
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/accountPayable/{id}/payment",
     *     tags={"Account Payables"},
     *     summary="Store a payment",
     *     description="Store a payment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Account payable id", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody( required=true, description="Store a payment", @OA\JsonContent(ref="#/components/schemas/StorePayableMovementRequest")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/AccountPayableResource")),
     *     @OA\Response(response="401", ref="#/components/schemas/Unauthenticated"),
     *     @OA\Response(response="404", description="Account payable not found"),
     *     @OA\Response(response="422", ref="#/components/schemas/ValidationError")
     * )
     */
    public function storePayment(StoreMovementRequest $request, int $id)
    {
        $accountPayable = AccountPayable::find($id);
        if (!$accountPayable) return response()->json(['error' => 'Cuenta por pagar no encontrada'], 404);
        if ($accountPayable->balance <= 0) {
            return response()->json(['error' => 'No se puede registrar un pago en una cuenta por cobrar con saldo 0'], 422);
        }
        $totalPayment = $request->cash + $request->yape + $request->plin + $request->card + $request->deposit;
        if ($totalPayment == 0) {
            return response()->json(['error' => 'Debe ingresar al menos un monto de pago'], 422);
        }
        // Obtén el tipo de cambio
        $exchangeRate = Currency::where('date', $request->paymentDate)->first();

        if ($request->input('currencyType') == 'USD') {
            if (!$exchangeRate) {
                return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha seleccionada'], 422);
            }
        }

        // Convertir el pago a la moneda de la cuenta por cobrar
        $totalInPayableCurrency = $totalPayment;
        $accountPayableCurrency = $accountPayable->order->currencyType;
        // Verificar si el pago se realizó en una moneda diferente a la de la cuenta por pagar
        if ($request->currencyType != $accountPayableCurrency) {
            if ($request->currencyType == 'PEN' && $accountPayableCurrency == 'USD') {
                // Convertir el monto pagado de PEN a USD
                $totalInPayableCurrency = round($totalPayment / $exchangeRate->saleRate, 2);
            } elseif ($request->currencyType == 'USD' && $accountPayableCurrency == 'PEN') {
                // Convertir el monto pagado de USD a PEN
                $totalInPayableCurrency = round($totalPayment * $exchangeRate->buyRate, 2);
            }
        }

        // Validar que el monto convertido no exceda el saldo
        if ($accountPayable->balance < $totalInPayableCurrency) {
            return response()->json([
                'error' => 'El monto a pagar excede el saldo de la cuenta por cobrar. Saldo actual: ' . $accountPayable->balance
            ], 422);
        }

        // Crear el movimiento
        $data = [
            'number' => $this->nextCorrelative(Movement::class, 'number'),
            'paymentDate' => $request->paymentDate,
            'cash' => $request->cash,
            'yape' => $request->yape,
            'plin' => $request->plin,
            'card' => $request->card,
            'deposit' => $request->isBankPayment ? $request->deposit : 0,
            'isBankPayment' => $request->isBankPayment,
            'comment' => $request->comment,
            'bank_id' => $request->bank_id,
            'accountPayable_id' => $id,
            'user_id' => auth()->user()->id,
            'currencyType' => $request->currencyType,
            'currency_id' => $exchangeRate->id,
            'total' => $totalPayment,
        ];

        $movement = Movement::create($data);

        // Actualizar el balance de la cuenta por pagar en su moneda original
        $accountPayable->balance -= $totalInPayableCurrency;
        $accountPayable->save();

        $purchase = $accountPayable->order;
        $purchase->balance -= $totalInPayableCurrency;
        $purchase->save();

        return response()->json(Movement::find($movement->id));
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/accountPayable/{id}",
     *     tags={"Account Payables"},
     *     summary="Get an account payable",
     *     description="Get an account payable",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Account payable id", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/AccountPayableResource")),
     *     @OA\Response(response="401", ref="#/components/schemas/Unauthenticated"),
     *     @OA\Response(response="404", description="Account payable not found")
     * )
     */
    public function show(int $id)
    {
        $accountPayable = AccountPayable::with(['movements'])->find($id);
        if (!$accountPayable) return response()->json(['error' => 'Cuenta por pagar no encontrada'], 404);
        return response()->json((new AccountPayableResource($accountPayable))->withPayable());
    }

    public function update(UpdateAccountPayableRequest $request, int $id)
    {
        //
    }

    public function destroy(int $id)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/accountPayable/deletePayment/{paymentId}",
     *     tags={"Account Payables"},
     *     summary="Delete a payment",
     *     description="Delete a payment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="paymentId", in="path", description="Payment id", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(@OA\Property(property="message", type="string", example="Pago eliminado correctamente"))),
     *     @OA\Response(response="401", ref="#/components/schemas/Unauthenticated"),
     *     @OA\Response(response="404", description="Payment not found", @OA\JsonContent(@OA\Property(property="error", type="string", example="Pago no encontrado")))
     * )
     */
    public function deletePayment(int $id)
    {
        $movement = Movement::find($id);
        if (!$movement) return response()->json(['error' => 'Pago no encontrado'], 404);

        $accountPayable = AccountPayable::find($movement->accountPayable_id);
        if (!$accountPayable) return response()->json(['error' => 'Cuenta por pagar no encontrada'], 404);

        // Obtener el tipo de cambio para la fecha del pago
        $exchangeRate = Currency::where('date', $movement->paymentDate)->first();

        if (!$exchangeRate) {
            return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha del pago'], 422);
        }

        $accountPayableCurrency = $accountPayable->order->currencyType;
        $totalInPayableCurrency = $movement->total;

        // Convertir el monto de la moneda del pago a la moneda de la cuenta por pagar si es necesario
        if ($movement->currencyType != $accountPayableCurrency) {
            if ($movement->currencyType == 'PEN' && $accountPayableCurrency == 'USD') {
                $totalInPayableCurrency = round($movement->total / $exchangeRate->saleRate, 2);
            } elseif ($movement->currencyType == 'USD' && $accountPayableCurrency == 'PEN') {
                $totalInPayableCurrency = round($movement->total * $exchangeRate->buyRate, 2);
            }
        }

        // Actualizar el balance de la cuenta por pagar con el monto convertido
        $accountPayable->balance += $totalInPayableCurrency;
        $accountPayable->save();

        $purchase = $accountPayable->order;
        $purchase->balance += $totalInPayableCurrency;
        $purchase->save();

        // Eliminar el movimiento
        $movement->delete();

        return response()->json(['message' => 'Pago eliminado correctamente']);
    }

}
