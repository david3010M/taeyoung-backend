<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexMachineryPurchaseRequest;
use App\Http\Requests\StoreMachineryPurchaseRequest;
use App\Http\Requests\UpdateMachineryPurchaseRequest;
use App\Http\Resources\MachineryPurchaseResource;
use App\Models\Order;

class MachineryPurchaseController extends Controller
{
    public function index(IndexMachineryPurchaseRequest $request)
    {
        return $this->getFilteredResults(
            Order::class,
            $request,
            Order::filtersMachineryPurchase,
            Order::sortMachineryPurchase,
            MachineryPurchaseResource::class
        );
    }

    public function store(StoreMachineryPurchaseRequest $request)
    {

    }

    public function show(string $id)
    {
        $machineryPurchase = Order::find($id);
        if (!$machineryPurchase) return response()->json(['message' => 'Machinery purchase not found'], 404);
        return response()->json(new MachineryPurchaseResource($machineryPurchase));
    }

    public function update(UpdateMachineryPurchaseRequest $request, string $id)
    {

    }

    public function destroy(string $id)
    {

    }
}
