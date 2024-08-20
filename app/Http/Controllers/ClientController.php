<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexClientRequest;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Person;

class ClientController extends Controller
{
    /**
     * SHOW ALL CLIENTS
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/client",
     *     tags={"Client"},
     *     security={{"bearerAuth": {}}},
     *     summary="Show all clients",
     *     @OA\Response(
     *         response=200,
     *         description="Show all clients",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ClientPagination")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The pagination must be an integer.")
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

    public function store(StoreClientRequest $request)
    {
        if ($request->typeDocument === 'DNI') $request->merge(['ruc' => null, 'businessName' => null, 'representativeDni' => null, 'representativeNames' => null]);
        else $request->merge(['dni' => null, 'names' => null, 'fatherSurname' => null, 'motherSurname' => null]);
        $request->merge(['type' => 'client']);
        $client = Person::create($request->all());
        $client->update(['filterName' => $request->typeDocument === 'DNI' ? $request->names . ' ' . $request->fatherSurname . ' ' . $request->motherSurname : $request->businessName]);
        $client = Person::with('country')->find($client->id);
        return response()->json($client);
    }

    public function show(string $id)
    {
        $client = Person::with('country')->where('type', 'client')->find($id);
        if (!$client) return response()->json(['message' => 'Client not found'], 404);
        return response()->json($client);
    }

    public function update(UpdateClientRequest $request, string $id)
    {
        $client = Person::where('type', 'client')->find($id);
        if (!$client) return response()->json(['message' => 'Client not found'], 404);
        if ($request->typeDocument === 'DNI') $request->merge(['ruc' => null, 'businessName' => null, 'representativeDni' => null, 'representativeNames' => null]);
        else $request->merge(['dni' => null, 'names' => null, 'fatherSurname' => null, 'motherSurname' => null]);
        $client->update($request->all());
        $client->update(['filterName' => $request->typeDocument === 'DNI' ? $request->names . ' ' . $request->fatherSurname . ' ' . $request->motherSurname : $request->businessName]);
        $client = Person::with('country')->where('type', 'client')->find($id);
        return response()->json($client);
    }

    public function destroy(string $id)
    {
        $client = Person::where('type', 'client')->find($id);
        if (!$client) return response()->json(['message' => 'Client not found'], 404);
        $client->delete();
        return response()->json(['message' => 'Client deleted']);
    }
}
