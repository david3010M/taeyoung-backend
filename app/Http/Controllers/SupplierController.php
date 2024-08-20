<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexSupplierRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Person;

class SupplierController extends Controller
{
    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/supplier",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Show all suppliers",
     *     @OA\Parameter(name="pagination", in="query", description="Number of items to display per page", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="ruc", in="query", description="Supplier RUC", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="businessName", in="query", description="Supplier business name", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="query", description="Supplier email", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="phone", in="query", description="Supplier phone", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="countryId", in="query", description="Supplier country ID", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="all", in="query", description="Show all suppliers", required=false, @OA\Schema(type="string", enum={"true", "false"})),
     *     @OA\Response(
     *         response=200,
     *         description="Show all suppliers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SupplierPagination")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function index(IndexSupplierRequest $request)
    {
        return $this->getFilteredResults(
            Person::where('type', 'supplier'),
            $request,
            Person::supplierFilters,
            Person::supplierSorts,
            SupplierResource::class
        );
    }

    /**
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/supplier",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Create a new supplier",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SupplierRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function store(StoreSupplierRequest $request)
    {
        if ($request->typeDocument === 'DNI') $request->merge(['ruc' => null, 'businessName' => null, 'representativeDni' => null, 'representativeNames' => null]);
        else $request->merge(['dni' => null, 'names' => null, 'fatherSurname' => null, 'motherSurname' => null]);
        $request->merge(['type' => 'supplier']);
        $supplier = Person::create($request->all());
        $supplier->update(['filterName' => $request->typeDocument === 'DNI' ? $request->names . ' ' . $request->fatherSurname . ' ' . $request->motherSurname : $request->businessName]);
        $supplier = Person::with('country')->find($supplier->id);
        return response()->json($supplier);
    }

    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/supplier/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Show a supplier",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show a supplier",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $supplier = Person::with('country')->where('type', 'supplier')->find($id);
        if (!$supplier) return response()->json(['error' => 'Supplier not found'], 404);
        return response()->json($supplier);
    }

    /**
     * @OA\Put (
     *     path="/taeyoung-backend/public/api/supplier/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Update a supplier",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SupplierRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function update(UpdateSupplierRequest $request, int $id)
    {
        $supplier = Person::where('type', 'supplier')->find($id);
        if (!$supplier) return response()->json(['message' => 'Supplier not found'], 404);
        if ($request->typeDocument === 'DNI') $request->merge(['ruc' => null, 'businessName' => null, 'representativeDni' => null, 'representativeNames' => null]);
        else $request->merge(['dni' => null, 'names' => null, 'fatherSurname' => null, 'motherSurname' => null]);
        $supplier->update($request->all());
        $supplier->update(['filterName' => $request->typeDocument === 'DNI' ? $request->names . ' ' . $request->fatherSurname . ' ' . $request->motherSurname : $request->businessName]);
        $supplier = Person::with('country')->where('type', 'supplier')->find($id);
        return response()->json($supplier);
    }

    /**
     * @OA\Delete (
     *     path="/taeyoung-backend/public/api/supplier/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Delete a supplier",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $supplier = Person::where('type', 'supplier')->find($id);
        if (!$supplier) return response()->json(['message' => 'Supplier not found'], 404);
        $supplier->delete();
        return response()->json(['message' => 'Supplier deleted']);
    }
}
