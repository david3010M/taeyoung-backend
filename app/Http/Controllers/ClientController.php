<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexClientRequest;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Imports\ClientsImport;
use Illuminate\Http\Request;
use App\Models\Person;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    /**
     * SHOW ALL CLIENTS
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/client",
     *     tags={"Client"},
     *     security={{"bearerAuth": {}}},
     *     summary="Show all clients",
     *     description="Show all clients",
     *     @OA\Parameter(parameter="ruc", name="ruc", in="query", required=false, description="Filter by ruc", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="filterName", name="filterName", in="query", required=false, description="Filter by business name", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="email", name="email", in="query", required=false, description="Filter by email", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="phone", name="phone", in="query", required=false, description="Filter by phone", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="country_id", name="country_id", in="query", required=false, description="Filter by country", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="page", name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="per_page", name="per_page", in="query", required=false, description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string", enum={"ruc","filterName","email","phone","country_id"})),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter(parameter="all", name="all", in="query", required=false, description="Get all clients", @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ClientCollection"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexClientRequest $request)
    {
        return $this->getFilteredResults(
            Person::where('type', 'client'),
            $request,
            Person::clientFilters,
            Person::clientSorts,
            ClientResource::class
        );
    }

    /**
     * CREATE A NEW CLIENT
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/client",
     *     tags={"Client"},
     *     security={{"bearerAuth": {}}},
     *     summary="Create a new client",
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/StoreClientRequest")),
     *     @OA\Response(response=200, description="Client created successfully", @OA\JsonContent(ref="#/components/schemas/Client")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function store(StoreClientRequest $request)
    {
        if ($request->typeDocument === 'DNI') $request->merge(['ruc' => null, 'businessName' => null, 'representativeDni' => null, 'representativeNames' => null]);
        else $request->merge(['dni' => null, 'names' => null, 'fatherSurname' => null, 'motherSurname' => null]);
        $request->merge(['type' => 'client']);
        $client = Person::create($request->all());
        $client->update(['filterName' => $client->typeDocument === 'DNI' ? $client->names . ' ' . $client->fatherSurname . ' ' . $client->motherSurname : $client->businessName]);
        $client = Person::with(['country', 'province'])->find($client->id);
        return response()->json($client);
    }

    /**
     * SHOW A CLIENT
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/client/{id}",
     *     tags={"Client"},
     *     security={{"bearerAuth": {}}},
     *     summary="Show a client",
     *     description="Show a client",
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Client ID", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Client")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Client not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Client not found")))
     * )
     */
    public function show(string $id)
    {
        $client = Person::with(['country', 'province'])->where('type', 'client')->find($id);
        if (!$client) return response()->json(['message' => 'Cliente no encontrado'], 404);
        return response()->json($client);
    }

    /**
     * UPDATE A CLIENT
     * @OA\Put (
     *     path="/taeyoung-backend/public/api/client/{id}",
     *     tags={"Client"},
     *     security={{"bearerAuth": {}}},
     *     summary="Update a client",
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Client ID", @OA\Schema(type="string")),
     *     @OA\RequestBody( required=true, @OA\JsonContent(ref="#/components/schemas/UpdateClientRequest")),
     *     @OA\Response(response=200, description="Client updated successfully", @OA\JsonContent(ref="#/components/schemas/Client")),
     *     @OA\Response(response=404, description="Client not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Client not found"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function update(UpdateClientRequest $request, string $id)
    {
        $client = Person::where('type', 'client')->find($id);
        if (!$client) return response()->json(['message' => 'Cliente no encontrado'], 404);
        if ($request->typeDocument === 'DNI') $request->merge(['ruc' => null, 'businessName' => null, 'representativeDni' => null, 'representativeNames' => null]);
        else $request->merge(['dni' => null, 'names' => null, 'fatherSurname' => null, 'motherSurname' => null]);
        $client->update($request->all());
        $client->update(['filterName' => $client->typeDocument === 'DNI' ? $client->names . ' ' . $client->fatherSurname . ' ' . $client->motherSurname : $client->businessName]);
        $client = Person::with(['country', 'province'])->where('type', 'client')->find($id);
        return response()->json($client);
    }

    /**
     * DELETE A CLIENT
     * @OA\Delete (
     *     path="/taeyoung-backend/public/api/client/{id}",
     *     tags={"Client"},
     *     security={{"bearerAuth": {}}},
     *     summary="Delete a client",
     *     description="Delete a client",
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Client ID", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Client deleted", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Client deleted"))),
     *     @OA\Response(response=404, description="Client not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Client not found"))),
     *     @OA\Response(response=422, description="Client has quotations or sales associated", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Client has quotations or sales associated"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function destroy(string $id)
    {
        $client = Person::where('type', 'client')->find($id);
        if (!$client) return response()->json(['message' => 'Cliente no encontrado'], 404);
        if ($client->quotations()->count() > 0) return response()->json(['message' => 'El cliente tiene cotizaciones asociadas'], 422);
        if ($client->sales()->count() > 0) return response()->json(['message' => 'El cliente tiene ventas asociadas'], 422);
        $client->delete();
        return response()->json(['message' => 'Client deleted']);
    }

    /**
     * IMPORT CLIENTS FROM EXCEL
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/client/excel",
     *     tags={"Client"},
     *     security={{"bearerAuth": {}}},
     *     summary="Import clients from excel",
     *     description="Import clients from excel",
     *     @OA\RequestBody( required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(@OA\Property(property="file", type="file")))),
     *     @OA\Response(response=200, description="Clients imported successfully", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Clients imported successfully"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        Excel::import(new ClientsImport, $request->file('file'));

        return response()->json(['message' => 'Clients imported successfully']);
    }


}
