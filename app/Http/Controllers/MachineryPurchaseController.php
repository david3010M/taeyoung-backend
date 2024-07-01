<?php

namespace App\Http\Controllers;

use App\Http\Resources\MachineryPurchaseResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MachineryPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $validator = validator($request->query(), [
            'pagination' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $pagination = $request->query('pagination', 5);

        $machineryPurchases = Order::with('supplier.country')->
        where('type', 'machinery_purchase')
            ->orderBy('id', 'desc')
            ->paginate($pagination);

        return MachineryPurchaseResource::collection($machineryPurchases);

    }

    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'number' => 'required|string',
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

    public function show(string $id)
    {
        $machineryPurchase = Order::with('supplier.country')->find($id);

        if ($machineryPurchase === null) {
            return response()->json(['error' => 'Machinery Purchase not found'], 404);
        }

        return response()->json(MachineryPurchaseResource::make($machineryPurchase));
    }

    public function update(Request $request, string $id)
    {
        $machineryPurchase = Order::find($id);

        if ($machineryPurchase === null) {
            return response()->json(['error' => 'Machinery Purchase not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'number' => 'required|string',
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
