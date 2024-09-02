<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexSparePartRequest;
use App\Http\Requests\StoreSparePartRequest;
use App\Http\Requests\UpdateSparePartRequest;
use App\Http\Resources\QuotationResource;
use App\Http\Resources\SparePartResource;
use App\Models\Quotation;
use App\Models\SparePart;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SparePartController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/sparePart",
     *     summary="List all spare parts",
     *     tags={"Spare Parts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort", in="query", description="Sort by column", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="direction", in="query", description="Sort direction", required=false, @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter(name="all", in="query", description="Get all items", required=false, @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="List of spare parts", @OA\JsonContent(ref="#/components/schemas/SparePartPagination")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function index(IndexSparePartRequest $request)
    {
        return $this->getFilteredResults(
            SparePart::class,
            $request,
            SparePart::filters,
            SparePart::sorts,
            SparePartResource::class
        );
    }

    /**
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/sparePart",
     *     summary="Create a spare part",
     *     tags={"Spare Parts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/SparePartRequest")),
     *     @OA\Response(response=200, description="Spare part created", @OA\JsonContent(ref="#/components/schemas/SparePartResource")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=401, description="UnSauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function store(StoreSparePartRequest $request)
    {
        $data = $request->validated();
        $data['code'] = $this->nextCorrelative(SparePart::class, 'code');
        $sparePart = SparePart::create($data);
        $sparePart = SparePart::find($sparePart->id);
        return response()->json(new SparePartResource($sparePart));
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/sparePart/{id}",
     *     summary="Show a spare part",
     *     tags={"Spare Parts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Spare part ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Spare part detail", @OA\JsonContent(ref="#/components/schemas/SparePartResource")),
     *     @OA\Response(response=404, description="Spare part not found", @OA\JsonContent(@OA\Property(property="message", type="string", example="Spare part not found"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function show(int $id)
    {
        $sparePart = SparePart::find($id);
        if (!$sparePart) return response()->json(['message' => 'Spare part not found'], 404);
        return response()->json(new SparePartResource($sparePart));
    }

    /**
     * @OA\Put(
     *     path="/taeyoung-backend/public/api/sparePart/{id}",
     *     summary="Update a spare part",
     *     tags={"Spare Parts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Spare part ID", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/SparePartRequest")),
     *     @OA\Response(response=200, description="Spare part updated", @OA\JsonContent(ref="#/components/schemas/SparePartResource")),
     *     @OA\Response(response=404, description="Spare part not found", @OA\JsonContent(@OA\Property(property="message", type="string", example="Spare part not found"))),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     *
     */
    public function update(UpdateSparePartRequest $request, int $id)
    {
        $sparePart = SparePart::find($id);
        if (!$sparePart) return response()->json(['message' => 'Spare part not found'], 404);
        $sparePart->update($request->validated());
        $sparePart = SparePart::find($sparePart->id);
        return response()->json(new SparePartResource($sparePart));
    }

    /**
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/sparePart/{id}",
     *     summary="Delete a spare part",
     *     tags={"Spare Parts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter( name="id", in="path", description="Spare part ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Spare part deleted", @OA\JsonContent(@OA\Property(property="message", type="string", example="Spare part deleted"))),
     *     @OA\Response(response=404, description="Spare part not found", @OA\JsonContent(@OA\Property(property="message", type="string", example="Spare part not found"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function destroy(int $id)
    {
        $sparePart = SparePart::find($id);
        if ($sparePart === null) return response()->json(['message' => 'Spare part not found'], 404);
        $sparePart->delete();
        return response()->json(['message' => 'Spare part deleted']);
    }
}
