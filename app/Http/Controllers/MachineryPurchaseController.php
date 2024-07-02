<?php

namespace App\Http\Controllers;

use App\Http\Resources\MachineryPurchaseResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MachineryPurchaseController extends Controller
{
    /**
     * GET ALL MACHINE PURCHASES
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/machinerypurchase",
     *     tags={"Machinery Purchase"},
     *     summary="Get all machinery purchases",
     *     description="Returns all machinery purchases",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="per_page", in="query", description="Number of items per page", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="businessName", in="query", description="Supplier business name", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="country_id", in="query", description="Supplier country id", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="number", in="query", description="Order number", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="date", in="query", description="Order date", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Machinery purchases retrieved", @OA\JsonContent(ref="#/components/schemas/MachineryPurchaseResourceCollection")),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="422", description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     *
     */
    public function index(Request $request)
    {
        $validator = validator($request->query(), [
            'per_page' => 'nullable|integer',
            'businessName' => 'nullable|string',
            'country_id' => 'nullable|integer',
            'number' => 'nullable|string',
            'date' => 'nullable|date',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $pagination = $request->query('per_page', 5);

        $machineryPurchases = Order::with('supplier.country')
            ->where('type', 'machinery_purchase')
            ->where('number', 'like', '%' . $request->query('number', '') . '%')
            ->where('date', 'like', '%' . $request->query('date', '') . '%')
            ->whereHas('supplier', function ($query) use ($request) {
                $query->where('businessName', 'like', '%' . $request->query('businessName', '') . '%');
                $query->where('country_id', 'like', '%' . $request->query('country_id', '') . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate($pagination);

        return MachineryPurchaseResource::collection($machineryPurchases);

    }

    /**
     * CREATE A NEW MACHINE PURCHASE
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/machinerypurchase",
     *     tags={"Machinery Purchase"},
     *     summary="Create a new machinery purchase",
     *     description="Create a new machinery purchase",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"number", "date", "quantity", "features", "total", "supplier_id"},
     *             @OA\Property(property="number", type="string", example="123456"),
     *             @OA\Property(property="date", type="string", example="2021-09-01"),
     *             @OA\Property(property="quantity", type="integer", example="1"),
     *             @OA\Property(property="features", type="string", example="Features"),
     *             @OA\Property(property="total", type="number", example="1000.00"),
     *             @OA\Property(property="supplier_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Machinery purchase created", @OA\JsonContent(ref="#/components/schemas/MachineryPurchaseResource")),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="422", description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'number' => [
                'required',
                'string',
                Rule::unique('orders', 'number')->where('type', 'machinery_purchase')
                    ->whereNull('deleted_at'),
            ],
            'date' => 'required|date',
            'quantity' => 'required|integer',
            'features' => 'required|string',
            'total' => 'required|numeric',
            'supplier_id' => [
                'required',
                Rule::exists('people', 'id')->where('type', 'supplier'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

//        $tipo = 'COMAQ';
//        $resultado = DB::select('SELECT COALESCE(MAX(CAST(SUBSTRING(number, LOCATE("-", number) + 1) AS SIGNED)), 0) + 1 AS siguienteNum FROM orders a WHERE SUBSTRING(number, 1, 5) = ?', [$tipo])[0]->siguienteNum;
//        $siguienteNum = (int)$resultado;


        $data = [
            'type' => 'machinery_purchase',
            'date' => $request->input('date'),
//            'number' => $tipo . "-" . str_pad($siguienteNum, 8, '0', STR_PAD_LEFT),
            'number' => $request->input('number'),
//            'documentType' => 'invoice',
            'quantity' => $request->input('quantity'),
            'detail' => $request->input('features'),
            'totalIncome' => 0,
            'totalExpense' => $request->input('total'),
            'currency' => 'Soles',
//            'typePayment' => 'credit',
//            'comment' => 'Machinery Purchase',
            'supplier_id' => $request->input('supplier_id'),
        ];

        $machineryPurchase = Order::create($data);
        $machineryPurchase = Order::with('supplier.country')->find($machineryPurchase->id);

        return response()->json($machineryPurchase);
    }

    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/machinerypurchase/{id}",
     *     tags={"Machinery Purchase"},
     *     summary="Get machinery purchase",
     *     description="Returns the machinery purchase identified by the given id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Machinery purchase id", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Machinery purchase retrieved", @OA\JsonContent(ref="#/components/schemas/MachineryPurchaseResource")),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="404", description="Machinery purchase not found", @OA\JsonContent(
     *         @OA\Property(property="error", type="string", example="Machinery Purchase not found")
     *     ))
     * )
     */
    public function show(string $id)
    {
        $machineryPurchase = Order::with('supplier.country')->find($id);

        if ($machineryPurchase === null) {
            return response()->json(['error' => 'Machinery Purchase not found'], 404);
        }

        return response()->json(MachineryPurchaseResource::make($machineryPurchase));
    }

    /**
     * @OA\Put (
     *     path="/taeyoung-backend/public/api/machinerypurchase/{id}",
     *     tags={"Machinery Purchase"},
     *     summary="Update machinery purchase",
     *     description="Update machinery purchase",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Machinery purchase id", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/MachineryPurchaseRequest")),
     *     @OA\Response(response="200", description="Machinery purchase updated", @OA\JsonContent(ref="#/components/schemas/MachineryPurchaseResource")),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="404", description="Machinery purchase not found", @OA\JsonContent(
     *         @OA\Property(property="error", type="string", example="Machinery Purchase not found")
     *     )),
     *     @OA\Response(response="422", description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(Request $request, string $id)
    {
        $machineryPurchase = Order::find($id);

        if ($machineryPurchase === null) {
            return response()->json(['error' => 'Machinery Purchase not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'number' => [
                'required',
                'string',
                Rule::unique('orders', 'number')->where('type', 'machinery_purchase')
                    ->whereNull('deleted_at')->ignore($machineryPurchase->id),
            ],
            'date' => 'required|date',
            'supplier_id' => [
                'required',
                Rule::exists('people', 'id')->where('type', 'supplier'),
            ],
            'quantity' => 'required|integer',
            'features' => 'required|string',
            'total' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'date' => $request->input('date'),
            'number' => $request->input('number'),
            'quantity' => $request->input('quantity'),
            'detail' => $request->input('features'),
            'totalExpense' => $request->input('total'),
            'supplier_id' => $request->input('supplier_id'),
        ];

        $machineryPurchase->update($data);
        $machineryPurchase = Order::with('supplier.country')->find($machineryPurchase->id);

        return response()->json($machineryPurchase);
    }

    /**
     * @OA\Delete (
     *     path="/taeyoung-backend/public/api/machinerypurchase/{id}",
     *     tags={"Machinery Purchase"},
     *     summary="Delete machinery purchase",
     *     description="Delete machinery purchase",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="Machinery purchase id", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Machinery purchase deleted", @OA\JsonContent(@OA\Property(property="message", type="string", example="Machinery Purchase deleted"))),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response="404", description="Machinery purchase not found", @OA\JsonContent(
     *         @OA\Property(property="error", type="string", example="Machinery Purchase not found")
     *     ))
     * )
     */
    public function destroy(string $id)
    {
        $machineryPurchase = Order::find($id);

        if ($machineryPurchase === null) {
            return response()->json(['error' => 'Machinery Purchase not found'], 404);
        }

        $machineryPurchase->delete();

        return response()->json(['message' => 'Machinery Purchase deleted']);
    }
}
