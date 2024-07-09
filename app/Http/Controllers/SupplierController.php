<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierResource;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
    public function index(Request $request)
    {
        $validator = validator($request->query(), [
            'pagination' => 'integer',
            'ruc' => 'nullable|integer',
            'businessName' => 'nullable|string',
            'email' => 'nullable|string',
            'phone' => 'nullable|integer',
            'countryId' => 'nullable|string',
            'all' => 'nullable|string|in:true,false',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $pagination = $request->query('pagination', 5);
        $all = $request->query('all', false) == 'true';

        $suppliers = Person::with('country')->where('type', 'supplier')
            ->where('ruc', 'like', '%' . $request->query('ruc') . '%')
            ->where('businessName', 'like', '%' . $request->query('businessName') . '%')
            ->where('email', 'like', '%' . $request->query('email') . '%')
            ->where('phone', 'like', '%' . $request->query('phone') . '%')
            ->where('country_id', 'like', '%' . $request->query('countryId') . '%');

        if (!$all) {
            $suppliers = $suppliers->paginate($pagination);
        } else {
            $suppliers = $suppliers->get();
        }

        return SupplierResource::collection($suppliers);
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
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'ruc' => [
                'required',
                'string',
                'max:11',
                Rule::unique('people')->where('type', 'supplier')
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
            'type' => 'supplier',
            'ruc' => $request->input('ruc'),
            'businessName' => $request->input('businessName'),
//            'address' => $request->input('address'),
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
    public function show(string $id)
    {
        $supplier = Person::with('country')->where('type', 'supplier')->find($id);
        if (!$supplier) {
            return response()->json(['error' => 'Supplier not found'], 404);
        }
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
    public function update(Request $request, string $id)
    {
        $supplier = Person::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'ruc' => [
                'required',
                'string',
                'max:11',
                Rule::unique('people')->where('type', 'supplier')
                    ->ignore($supplier->id, 'id')->whereNull('deleted_at')
            ],
            'businessName' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|int',
            'representativeDni' => 'nullable|string',
            'representativeNames' => 'nullable|string',
            'country_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'ruc' => $request->input('ruc'),
            'businessName' => $request->input('businessName'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'representativeDni' => $request->input('representativeDni'),
            'representativeNames' => $request->input('representativeNames'),
            'country_id' => $request->input('country_id')
        ];

        $supplier->update($data);
        $supplier = Person::with('country')->find($id);

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
    public function destroy(string $id)
    {
        $supplier = Person::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted successfully']);
    }
}
