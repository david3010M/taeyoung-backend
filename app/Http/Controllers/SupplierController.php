<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/supplier",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Show all suppliers",
     *     @OA\Response(
     *         response=200,
     *         description="Show all suppliers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Supplier")
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
    public function index()
    {
        return response()->json(Person::with('country')->where('type', 'supplier')->get());
    }

    /**
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/supplier",
     *     tags={"Supplier"},
     *     security={{"bearerAuth": {}}},
     *     summary="Create a new supplier",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="type", type="string", example="JURIDICA"),
     *             @OA\Property(property="ruc", type="string", example="20547869541"),
     *             @OA\Property(property="businessName", type="string", example="Distribuidora de Productos S.A."),
     *             @OA\Property(property="address", type="string", example="Jr. Los Pinos 123"),
     *             @OA\Property(property="email", type="string", example="supplier@gmail.com"),
     *             @OA\Property(property="phone", type="string", example="987654321"),
     *             @OA\Property(property="representativeDni", type="string", example="12345678"),
     *             @OA\Property(property="representativeNames", type="string", example="Juan Perez"),
     *             @OA\Property(property="country_id", type="integer", example="1")
     *         )
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
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'type' => 'required|string|in:supplier,client',
            'ruc' => 'required|string|length:11',
            'businessName' => 'required|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'representativeDni' => 'nullable|string',
            'representativeNames' => 'nullable|string',
            'country_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'type' => 'supplier',
            'ruc' => $request->input('ruc'),
            'businessName' => $request->input('businessName'),
            'address' => $request->input('address'),
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
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
