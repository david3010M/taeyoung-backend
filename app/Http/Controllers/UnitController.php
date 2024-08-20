<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;

class UnitController extends Controller
{
    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/unit",
     *     tags={"Unit"},
     *     summary="Get all units",
     *     description="Get all units",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="page", name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="per_page", name="per_page", in="query", required=false, description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter(parameter="name", name="name", in="query", required=false, description="Filter by name", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="abbreviation", name="abbreviation", in="query", required=false, description="Filter by abbreviation", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Unit"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexUnitRequest $request)
    {
        return $this->getFilteredResults(
            Unit::class,
            $request,
            Unit::filters,
            Unit::sorts,
            UnitResource::class
        );
    }

    /**
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/unit",
     *     tags={"Unit"},
     *     summary="Create a unit",
     *     description="Create a unit",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/StoreUnitRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Unit")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StoreUnitRequest $request)
    {
        $unit = Unit::create($request->validated());
        $unit = Unit::find($unit->id);
        return response()->json(new UnitResource($unit));
    }

    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/unit/{id}",
     *     tags={"Unit"},
     *     summary="Get a unit",
     *     description="Get a unit",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Unit ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Unit")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Unit not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unit not found")))
     * )
     */
    public function show(int $id)
    {
        $unit = Unit::find($id);
        if (!$unit) return response()->json(['message' => 'Unit not found'], 404);
        return response()->json(new UnitResource($unit));
    }

    /**
     * @OA\Put (
     *     path="/taeyoung-backend/public/api/unit/{id}",
     *     tags={"Unit"},
     *     summary="Update a unit",
     *     description="Update a unit",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Unit ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/UpdateUnitRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Unit")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Unit not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unit not found"))),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(UpdateUnitRequest $request, int $id)
    {
        $unit = Unit::find($id);
        if (!$unit) return response()->json(['message' => 'Unit not found'], 404);
        $unit->update($request->validated());
        $unit = Unit::find($unit->id);
        return response()->json(new UnitResource($unit));
    }

    /**
     * @OA\Delete (
     *     path="/taeyoung-backend/public/api/unit/{id}",
     *     tags={"Unit"},
     *     summary="Delete a unit",
     *     description="Delete a unit",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Unit ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unit deleted"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Unit not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unit not found"))),
     *     @OA\Response(response=409, description="Unit has spare parts", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unit has spare parts")))
     * )
     */
    public function destroy(int $id)
    {
        $unit = Unit::find($id);
        if (!$unit) return response()->json(['message' => 'Unit not found'], 404);
        if ($unit->spareParts()->count() > 0) return response()->json(['message' => 'Unit has spare parts'], 409);
        $unit->delete();
        return response()->json(['message' => 'Unit deleted']);
    }
}
