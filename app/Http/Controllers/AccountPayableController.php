<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexAccountPayableRequest;
use App\Http\Requests\StoreMovementRequest;
use App\Http\Resources\AccountPayableResource;
use App\Models\AccountPayable;
use App\Http\Requests\StoreAccountPayableRequest;
use App\Http\Requests\UpdateAccountPayableRequest;
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
     *     @OA\Parameter( name="date", in="query", description="Filter by date", @OA\Schema(type="string")),
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
        if ($request->cash == 0 && $request->yape == 0 && $request->plin == 0 && $request->card == 0 && $request->deposit == 0) {
            return response()->json(['error' => 'Debe ingresar al menos un monto de pago'], 422);
        }
        if ($accountPayable->balance <= 0) return response()->json(['error' => 'No se puede registrar un pago en una cuenta por pagar con saldo 0'], 422);
        if ($accountPayable->balance < $request->cash + $request->yape + $request->plin + $request->card + $request->deposit) {
            return response()->json(['error' => 'El monto a pagar es mayor al saldo de la cuenta por pagar ' . $accountPayable->balance], 422);
        }
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
        ];
        $movement = Movement::create($data);
        $movement->total = $movement->cash + $movement->yape + $movement->plin + $movement->card + $movement->deposit;
        $movement->save();

        $accountPayable->balance -= $movement->total;
        $accountPayable->save();

        $movement = Movement::find($movement->id);

        return response()->json($movement);
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
}
