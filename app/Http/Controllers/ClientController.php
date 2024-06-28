<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierResource;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
    public function index(Request $request)
    {
        $validator = validator($request->query(), [
            'pagination' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $pagination = $request->query('pagination', 5);

        $clients = Person::with('country')->where('type', 'client')->paginate($pagination);
        return SupplierResource::collection($clients);
    }

    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'ruc' => [
                'required',
                'string',
                'max:11',
                Rule::unique('people')->where('type', 'client')
                    ->whereNull('deleted_at')
            ],
            'businessName' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|integer',
            'representativeDni' => 'nullable|string',
            'representativeNames' => 'nullable|string',
            'country_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'type' => 'client',
            'ruc' => $request->input('ruc'),
            'businessName' => $request->input('businessName'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'representativeDni' => $request->input('representativeDni'),
            'representativeNames' => $request->input('representativeNames'),
            'country_id' => $request->input('country_id'),
        ];

        $supplier = Person::create($data);
        $supplier = Person::with('country')->find($supplier->id);

        return response()->json($supplier);
    }

    public function show(string $id)
    {
        $client = Person::with('country')->where('type', 'client')->find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        return response()->json($client);
    }

    public function update(Request $request, string $id)
    {
        $client = Person::where('type', 'client')->find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'ruc' => [
                'required',
                'string',
                'max:11',
                Rule::unique('people')->where('type', 'client')
                    ->whereNull('deleted_at')
                    ->ignore($id)
            ],
            'businessName' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|integer',
            'representativeDni' => 'nullable|string',
            'representativeNames' => 'nullable|string',
            'country_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'type' => 'client',
            'ruc' => $request->input('ruc'),
            'businessName' => $request->input('businessName'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'representativeDni' => $request->input('representativeDni'),
            'representativeNames' => $request->input('representativeNames'),
            'country_id' => $request->input('country_id'),
        ];

        $client->update($data);
        $client = Person::with('country')->where('type', 'client')->find($id);

        return response()->json($client);
    }

    public function destroy(string $id)
    {
        $client = Person::where('type', 'client')->find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $client->delete();

        return response()->json(['message' => 'Client deleted']);
    }
}
