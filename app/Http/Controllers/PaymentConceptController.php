<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexPaymentConceptRequest;
use App\Http\Resources\PaymentConceptResource;
use App\Models\PaymentConcept;
use App\Http\Requests\StorePaymentConceptRequest;
use App\Http\Requests\UpdatePaymentConceptRequest;

class PaymentConceptController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/paymentConcept",
     *     tags={"PaymentConcept"},
     *     summary="Get all payment concepts",
     *     description="Get all payment concepts",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="all", name="all", in="query", required=false, description="Get all units", @OA\Schema(type="boolean")),
     *     @OA\Parameter(parameter="page", name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="per_page", name="per_page", in="query", required=false, description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter(parameter="number", name="number", in="query", required=false, description="Filter by number", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="name", name="name", in="query", required=false, description="Filter by name", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="type", name="type", in="query", required=false, description="Filter by type", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PaymentConceptCollection")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexPaymentConceptRequest $request)
    {
        return $this->getFilteredResults(
            PaymentConcept::class,
            $request,
            PaymentConcept::filters,
            PaymentConcept::sorts,
            PaymentConceptResource::class
        );
    }

    /**
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/paymentConcept",
     *     tags={"PaymentConcept"},
     *     summary="Create a payment concept",
     *     description="Create a payment concept",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/StorePaymentConceptRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PaymentConcept")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StorePaymentConceptRequest $request)
    {
        $number = $this->nextCorrelative(PaymentConcept::class, 'number');
        $paymentConcept = PaymentConcept::create(array_merge($request->validated(), ['number' => $number]));
        $paymentConcept = PaymentConcept::find($paymentConcept->id);
        return response()->json(new PaymentConceptResource($paymentConcept));
    }

    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/paymentConcept/{id}",
     *     tags={"PaymentConcept"},
     *     summary="Get a payment concept",
     *     description="Get a payment concept",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Payment concept ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PaymentConcept")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Payment concept not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Payment concept not found")))
     * )
     */
    public function show(int $id)
    {
        $paymentConcept = PaymentConcept::find($id);
        if (!$paymentConcept) return response()->json(['message' => 'Payment concept not found'], 404);
        return response()->json(new PaymentConceptResource($paymentConcept));
    }

    /**
     * @OA\Put (
     *     path="/taeyoung-backend/public/api/paymentConcept/{id}",
     *     tags={"PaymentConcept"},
     *     summary="Update a payment concept",
     *     description="Update a payment concept",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Payment concept ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/UpdatePaymentConceptRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PaymentConcept")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Payment concept not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Payment concept not found")))
     * )
     */
    public function update(UpdatePaymentConceptRequest $request, int $id)
    {
        $paymentConcept = PaymentConcept::find($id);
        $paymentConcept->update($request->validated());
        $paymentConcept = PaymentConcept::find($paymentConcept->id);
        return response()->json(new PaymentConceptResource($paymentConcept));
    }

    /**
     * @OA\Delete (
     *     path="/taeyoung-backend/public/api/paymentConcept/{id}",
     *     tags={"PaymentConcept"},
     *     summary="Delete a payment concept",
     *     description="Delete a payment concept",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Payment concept ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Payment concept deleted"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Payment concept not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Payment concept not found")))
     * )
     */
    public function destroy(int $id)
    {
        $paymentConcept = PaymentConcept::find($id);
        if (!$paymentConcept) return response()->json(['message' => 'Payment concept not found'], 404);
        $paymentConcept->delete();
        return response()->json(['message' => 'Payment concept deleted']);
    }
}
