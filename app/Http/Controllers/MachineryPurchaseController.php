<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

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

        $machineryPurchases = Order::where('type', 'machineryPurchase')->paginate($pagination);

        return response()->json($machineryPurchases);

    }

    public function store(Request $request)
    {
        //
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
