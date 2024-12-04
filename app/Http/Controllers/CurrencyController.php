<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCurrencyRequest;
use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use App\Traits\Filterable;

class CurrencyController extends Controller
{
    use Filterable;

    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/currency",
     *     tags={"Currency"},
     *     summary="Get all currencies",
     *     description="Get all currencies",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="buyRate", in="query", description="Filter by buy rate", @OA\Schema(type="number")),
     *     @OA\Parameter( name="saleRate", in="query", description="Filter by sale rate", @OA\Schema(type="number")),
     *     @OA\Parameter( name="date", in="query", description="Filter by date", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter( name="sort", in="query", description="Sort by column", @OA\Schema(type="string")),
     *     @OA\Parameter( name="order", in="query", description="Sort by order", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter( name="page", in="query", description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter( name="per_page", in="query", description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/CurrencyCollection")),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="422", description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexCurrencyRequest $request)
    {
        return $this->getFilteredResults(
            Currency::class,
            $request,
            Currency::filters,
            Currency::sorts,
            CurrencyResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/currency",
     *     tags={"Currency"},
     *     summary="Create a currency",
     *     description="Create a currency",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(required=true, description="Pass currency data", @OA\JsonContent(ref="#/components/schemas/StoreCurrencyRequest")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/CurrencyResource")),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="422", description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StoreCurrencyRequest $request)
    {
        $currency = Currency::create($request->validated());
        return response()->json(new CurrencyResource($currency));
    }

    /**
     * Display the specified resource.
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/currency/{id}",
     *     tags={"Currency"},
     *     summary="Get a currency",
     *     description="Get a currency",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Currency ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/CurrencyResource")),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="404", description="Currency not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Currency not found")))
     * )
     */
    public function show(int $id)
    {
        $currency = Currency::find($id);
        if (!$currency) return response()->json(['message' => 'Currency not found'], 404);
        return response()->json(new CurrencyResource($currency));
    }

    /**
     * Update the specified resource in storage.
     * @OA\Put(
     *     path="/taeyoung-backend/public/api/currency/{id}",
     *     tags={"Currency"},
     *     summary="Update a currency",
     *     description="Update a currency",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Currency ID", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, description="Pass currency data", @OA\JsonContent(ref="#/components/schemas/UpdateCurrencyRequest")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(ref="#/components/schemas/CurrencyResource")),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="404", description="Currency not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Currency not found")))
     * )
     */
    public function update(UpdateCurrencyRequest $request, int $id)
    {
        $currency = Currency::find($id);
        if (!$currency) return response()->json(['message' => 'Currency not found'], 404);

        $references = $currency->order()->count() + $currency->accountPayable()->count()
            + $currency->accountReceivable()->count() + $currency->movements()->count();

        if ($references > 0) {
            return response()->json(['message' => 'El tipo de cambio no puede ser modificado porque tiene referencias'], 422);
        }

        $data = $request->validated();
        $data = array_filter($data, fn($value) => $value !== null);

        $currency->update($data);

        return response()->json(new CurrencyResource($currency));
    }

    /**
     * Remove the specified resource from storage.
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/currency/{id}",
     *     tags={"Currency"},
     *     summary="Delete a currency",
     *     description="Delete a currency",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Currency ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Success", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Currency deleted"))),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="404", description="Currency not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Currency not found")))
     * )
     */
    public function destroy(int $id)
    {
        $currency = Currency::find($id);
        if (!$currency) return response()->json(['message' => 'Currency not found'], 404);

        $references = $currency->order()->count() + $currency->accountPayable()->count()
            + $currency->accountReceivable()->count() + $currency->movements()->count();

        if ($references > 0) {
            return response()->json(['message' => 'El tipo de cambio no puede ser eliminado porque tiene referencias'], 422);
        }

        $currency->delete();
        return response()->json(['message' => 'Currency deleted']);
    }
}
