<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexAccountReceivableRequest;
use App\Http\Requests\StoreMovementRequest;
use App\Http\Resources\AccountReceivableResource;
use App\Models\AccountReceivable;
use App\Http\Requests\StoreAccountReceivableRequest;
use App\Http\Requests\UpdateAccountReceivableRequest;
use App\Models\Currency;
use App\Models\Movement;
use App\Models\Order;
use App\Utils\Constants;

class AccountReceivableController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/accountReceivable",
     *     tags={"Account Receivables"},
     *     summary="Get all account receivables",
     *     description="Get all account receivables",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="date", name="date[]", in="query", required=false, description="Account Receivable Date", @OA\Schema(type="array", @OA\Items(type="string", format="date"))),
     *     @OA\Parameter( name="amount", in="query", description="Filter by amount", @OA\Schema(type="string")),
     *     @OA\Parameter( name="balance", in="query", description="Filter by balance", @OA\Schema(type="string")),
     *     @OA\Parameter( name="status", in="query", description="Filter by status", @OA\Schema(type="string")),
     *     @OA\Parameter( name="client_id", in="query", description="Filter by client_id", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="client$filterName", in="query", description="Filter by client$filterName", @OA\Schema(type="string")),
     *     @OA\Parameter( name="client$country_id", in="query", description="Filter by client$country_id", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="order_id", in="query", description="Filter by order_id", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="currency$date", in="query", description="Filter by currency$date", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/AccountReceivableCollection")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function index(IndexAccountReceivableRequest $request)
    {
        return $this->getFilteredResults(
            AccountReceivable::class,
            $request,
            AccountReceivable::filters,
            AccountReceivable::sorts,
            AccountReceivableResource::class
        );
    }

    /**
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/accountReceivable/{id}/payment",
     *     tags={"Account Receivables"},
     *     summary="Store a payment",
     *     description="Store a payment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Account receivable id", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody( required=true, description="Store a payment", @OA\JsonContent(ref="#/components/schemas/StoreReceivableMovementRequest")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/AccountReceivableResource")),
     *     @OA\Response(response="401", ref="#/components/schemas/Unauthenticated"),
     *     @OA\Response(response="404", description="Account receivable not found"),
     *     @OA\Response(response="422", ref="#/components/schemas/ValidationError")
     * )
     */
    public function storePayment(StoreMovementRequest $request, int $id)
    {
        $accountReceivable = AccountReceivable::find($id);
        if (!$accountReceivable) return response()->json(['error' => 'Cuenta por cobrar no encontrada'], 404);

        if ($request->cash == 0 && $request->yape == 0 && $request->plin == 0 && $request->card == 0 && $request->deposit == 0) {
            return response()->json(['error' => 'Debe ingresar al menos un monto de pago'], 422);
        }

        if ($accountReceivable->balance <= 0) {
            return response()->json(['error' => 'No se puede registrar un pago en una cuenta por cobrar con saldo 0'], 422);
        }

        $totalPayment = $request->cash + $request->yape + $request->plin + $request->card + $request->deposit;

        // Obtén el tipo de cambio
        $exchangeRate = Currency::where('date', $request->paymentDate)->first();
        if (!$exchangeRate) {
            return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha seleccionada'], 422);
        }

        // Convertir el pago a la moneda de la cuenta por cobrar
        $totalInReceivableCurrency = $totalPayment;
        $accountReceivableCurrency = $accountReceivable->order->currencyType;
        if ($request->currencyType != $accountReceivableCurrency) {
            if ($request->currencyType == 'PEN' && $accountReceivableCurrency == 'USD') {
                $totalInReceivableCurrency = round($totalPayment / $exchangeRate->saleRate, 2);
            } elseif ($request->currencyType == 'USD' && $accountReceivableCurrency == 'PEN') {
                $totalInReceivableCurrency = round($totalPayment * $exchangeRate->buyRate, 2);
            }
        }

        // Validar que el monto convertido no exceda el saldo
        if ($accountReceivable->balance < $totalInReceivableCurrency) {
            return response()->json([
                'error' => 'El monto a pagar excede el saldo de la cuenta por cobrar. Saldo actual: ' . $accountReceivable->balance
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
            'accountReceivable_id' => $id,
            'user_id' => auth()->user()->id,
            'currencyType' => $request->currencyType,
            'currency_id' => $exchangeRate->id,
            'total' => $totalPayment,
        ];

        $movement = Movement::create($data);

        // Actualizar el balance de la cuenta por cobrar
        $accountReceivable->balance -= $totalInReceivableCurrency;
        $accountReceivable->save();

        $sale = $accountReceivable->order;
        $sale->balance -= $totalInReceivableCurrency;
        $sale->save();

        return response()->json(Movement::find($movement->id));
    }


    public function store(StoreAccountReceivableRequest $request)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/accountReceivable/{id}",
     *     tags={"Account Receivables"},
     *     summary="Get an account receivable",
     *     description="Get an account receivable",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Account receivable id", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/AccountReceivableResource")),
     *     @OA\Response(response="401", ref="#/components/schemas/Unauthenticated"),
     *     @OA\Response(response="404", description="Account receivable not found")
     * )
     */
    public function show(int $id)
    {
        $accountReceivable = AccountReceivable::with(['movements.currency', 'extensions'])->find($id);
        if (!$accountReceivable) return response()->json(['error' => 'Cuenta por cobrar no encontrada'], 404);
        return response()->json((new AccountReceivableResource($accountReceivable))->withReceivable());
    }

    public function update(UpdateAccountReceivableRequest $request, int $id)
    {

    }

    public function destroy(int $id)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/accountReceivable/deletePayment/{paymentId}",
     *     tags={"Account Receivables"},
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

        $accountReceivable = AccountReceivable::find($movement->accountReceivable_id);
        if (!$accountReceivable) return response()->json(['error' => 'Cuenta por cobrar no encontrada'], 404);

        // Obtener el tipo de cambio para la fecha del pago
        $exchangeRate = Currency::where('date', $movement->paymentDate)->first();
        if (!$exchangeRate) {
            return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha del pago'], 422);
        }

        $accountReceivableCurrency = $accountReceivable->order->currencyType;
        $totalInReceivableCurrency = $movement->total;

        // Convertir el monto de la moneda del pago a la moneda de la cuenta por pagar si es necesario
        if ($movement->currencyType != $accountReceivableCurrency) {
            if ($movement->currencyType == 'PEN' && $accountReceivableCurrency == 'USD') {
                $totalInReceivableCurrency = round($movement->total / $exchangeRate->saleRate, 2);
            } elseif ($movement->currencyType == 'USD' && $accountReceivableCurrency == 'PEN') {
                $totalInReceivableCurrency = round($movement->total * $exchangeRate->buyRate, 2);
            }
        }

        // Actualizar el balance de la cuenta por pagar con el monto convertido
        $accountReceivable->balance += $totalInReceivableCurrency;
        $accountReceivable->save();

        $sale = $accountReceivable->order;
        $sale->balance += $totalInReceivableCurrency;
        $sale->save();

        // Eliminar el movimiento
        $movement->delete();

        return response()->json(['message' => 'Pago eliminado correctamente']);
    }
}
