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
     *     summary="List all suppliers",
     *     description="Show all suppliers",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="ruc", name="ruc", in="query", required=false, description="Filter by ruc", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="filterName", name="filterName", in="query", required=false, description="Filter by business name", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="email", name="email", in="query", required=false, description="Filter by email", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="phone", name="phone", in="query", required=false, description="Filter by phone", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="country_id", name="country_id", in="query", required=false, description="Filter by country", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="page", name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="per_page", name="per_page", in="query", required=false, description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string", enum={"ruc","filterName","email","phone","country_id"})),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter(parameter="all", name="all", in="query", required=false, description="Get all suppliers", @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/SupplierCollection"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
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
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/StoreSupplierRequest")),
     *     @OA\Response(response=200, description="Supplier created successfully", @OA\JsonContent(ref="#/components/schemas/Supplier")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function store(StoreSupplierRequest $request)
    {
        if ($request->typeDocument === 'DNI') $request->merge(['ruc' => null, 'businessName' => null, 'representativeDni' => null, 'representativeNames' => null]);
        else $request->merge(['dni' => null, 'names' => null, 'fatherSurname' => null, 'motherSurname' => null]);
        $request->merge(['type' => 'supplier']);
        $supplier = Person::create($request->all());
        $supplier->update(['filterName' => $supplier->typeDocument === 'DNI' ? $supplier->names . ' ' . $supplier->fatherSurname . ' ' . $supplier->motherSurname : $supplier->businessName]);
        $supplier = Person::with('country')->find($supplier->id);
        return response()->json(new SupplierResource($supplier));
    }

    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/supplier/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Show a supplier",
     *     @OA\Parameter( name="id", in="path", required=true, description="Supplier ID", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Supplier")),
     *     @OA\Response( response=404, description="Supplier not found", @OA\JsonContent( @OA\Property(property="message", type="string", example="Supplier not found"))),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent( @OA\Property(property="error", type="string", example="Unauthenticated")))
     * )
     */
    public function show(int $id)
    {
        $supplier = Person::with('country')->where('type', 'supplier')->find($id);
        if (!$supplier) return response()->json(['error' => 'Proveedor no encontrado'], 404);
        return response()->json(new SupplierResource($supplier));
    }

    /**
     * @OA\Put (
     *     path="/taeyoung-backend/public/api/supplier/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Update a supplier",
     *     @OA\Parameter( name="id", in="path", required=true, description="Supplier ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/UpdateSupplierRequest")),
     *     @OA\Response( response=200, description="Supplier updated successfully", @OA\JsonContent(ref="#/components/schemas/Supplier")),
     *     @OA\Response( response=404, description="Supplier not found", @OA\JsonContent( @OA\Property(property="message", type="string", example="Supplier not found"))),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent( @OA\Property(property="error", type="string", example="Unauthenticated")))
     * )
     */
    public function update(UpdateSupplierRequest $request, int $id)
    {
        $supplier = Person::where('type', 'supplier')->find($id);
        if (!$supplier) return response()->json(['message' => 'Proveedor no encontrado'], 404);
        if ($request->typeDocument === 'DNI') $request->merge(['ruc' => null, 'businessName' => null, 'representativeDni' => null, 'representativeNames' => null]);
        else $request->merge(['dni' => null, 'names' => null, 'fatherSurname' => null, 'motherSurname' => null]);
        $supplier->update($request->all());
        $supplier->update(['filterName' => $supplier->typeDocument === 'DNI' ? $supplier->names . ' ' . $supplier->fatherSurname . ' ' . $supplier->motherSurname : $supplier->businessName]);
        $supplier = Person::with('country')->where('type', 'supplier')->find($id);
        return response()->json(new SupplierResource($supplier));
    }

    /**
     * @OA\Delete (
     *     path="/taeyoung-backend/public/api/supplier/{id}",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Delete a supplier",
     *     @OA\Parameter( name="id", in="path", required=true, description="Supplier ID", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Supplier deleted", @OA\JsonContent( @OA\Property(property="message", type="string", example="Supplier deleted"))),
     *     @OA\Response( response=404, description="Supplier not found", @OA\JsonContent( @OA\Property(property="message", type="string", example="Supplier not found"))),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent( @OA\Property(property="error", type="string", example="Unauthenticated")))
     * )
     */
    public function destroy(int $id)
    {
        $supplier = Person::where('type', 'supplier')->find($id);
        if (!$supplier) return response()->json(['message' => 'Proveedor no encontrado'], 404);
        if ($supplier->purchases()->count() > 0) return response()->json(['message' => 'El proveedor tiene compras asociadas'], 422);
        $supplier->delete();
        return response()->json(['message' => 'Proveedor eliminado']);
    }
}
