<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexAccountReceivableRequest;
use App\Http\Resources\AccountPayableResource;
use App\Models\AccountPayable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountPayableRequest;
use App\Http\Requests\UpdateAccountPayableRequest;

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
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/AccountReceivableResource")),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Unprocessable Entity")
     * )
     */
    public function index(IndexAccountReceivableRequest $request)
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

    public function show(int $id)
    {
        //
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
