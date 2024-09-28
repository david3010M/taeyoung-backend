<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexAccountReceivableRequest;
use App\Http\Resources\AccountReceivableResource;
use App\Models\AccountReceivable;
use App\Http\Requests\StoreAccountReceivableRequest;
use App\Http\Requests\UpdateAccountReceivableRequest;

class AccountReceivableController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/accountReceivable",
     *     tags={"Account Receivables"},
     *     summary="Get all account receivables",
     *     description="Get all account receivables",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="date", in="query", description="Filter by date", @OA\Schema(type="string")),
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

    public function store(StoreAccountReceivableRequest $request)
    {
        //
    }

    public function show(int $id)
    {
        $accountReceivable = AccountReceivable::find($id);
        if (!$accountReceivable) return response()->json(['error' => 'Cuenta por cobrar no encontrada'], 404);
        return response()->json(new AccountReceivableResource($accountReceivable));
    }

    public function update(UpdateAccountReceivableRequest $request, int $id)
    {

    }

    public function destroy(int $id)
    {
        //
    }
}
