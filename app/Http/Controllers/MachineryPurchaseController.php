<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexMachineryPurchaseRequest;
use App\Http\Requests\StoreMachineryPurchaseRequest;
use App\Http\Requests\UpdateMachineryPurchaseRequest;
use App\Http\Resources\MachineryPurchaseResource;
use App\Models\DetailMachinery;
use App\Models\DetailSparePart;
use App\Models\Order;
use App\Models\SparePart;

class MachineryPurchaseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/machineryPurchase",
     *     tags={"MachineryPurchase"},
     *     summary="List machinery purchases",
     *     description="Returns a list of machinery purchases.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="page", name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="per_page", name="per_page", in="query", required=false, description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="object",ref="#/components/schemas/MachineryPurchaseCollection")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     * )
     */
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


    /**
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/machineryPurchase",
     *     tags={"MachineryPurchase"},
     *     summary="Store machinery purchase",
     *     description="Store a new machinery purchase.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, description="Machinery purchase data", @OA\JsonContent(ref="#/components/schemas/StoreMachineryPurchaseRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/MachineryPurchaseResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StoreMachineryPurchaseRequest $request)
    {
        $dataQuotation = [
            'type' => 'machineryPurchase',
            'number' => $this->nextCorrelativeQuery(Order::where('type', 'machineryPurchase'), 'number'),
            'date' => $request->input('date'),
            'detail' => $request->input('detail'),
//            'discount' => $request->input('discount'),
            'currencyType' => $request->input('currencyType'),
            'supplier_id' => $request->input('supplier_id'),
            'quotation_id' => $request->input('quotation_id'),
        ];

        $machineryPurchase = Order::create($dataQuotation);
        $totalMachinery = 0;
        $totalSpareParts = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpares = $request->input('detailSpareParts');

        if ($detailMachinery) {
            foreach ($detailMachinery as $detail) {
                $detailMachinery = DetailMachinery::create([
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'movementType' => 'machineryPurchase',
                    'purchasePrice' => $detail['purchasePrice'],
                    'purchaseValue' => $detail['purchasePrice'] * $detail['quantity'],
                    'order_id' => $machineryPurchase->id,
                ]);
                $totalMachinery += $detailMachinery->purchasePrice * $detailMachinery->quantity;
            }
        }

        if ($detailSpares) {
            $totalSpareParts = $this->addDetailSpareParts($detailSpares, $machineryPurchase);
        }

        $machineryPurchase->totalMachinery = $totalMachinery;
        $machineryPurchase->totalSpareParts = $totalSpareParts;
        $machineryPurchase->subtotal = $totalMachinery + $totalSpareParts;
        $machineryPurchase->total = $totalMachinery + $totalSpareParts;
        $machineryPurchase->totalExpense = $totalMachinery + $totalSpareParts;
        $machineryPurchase->save();

        $machineryPurchase = Order::find($machineryPurchase->id);
        return response()->json(new MachineryPurchaseResource($machineryPurchase));
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/machineryPurchase/{id}",
     *     tags={"MachineryPurchase"},
     *     summary="Show machinery purchase",
     *     description="Returns a machinery purchase.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Machinery purchase ID", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/MachineryPurchaseResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Machinery purchase not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Machinery purchase not found")))
     * )
     */
    public function show(string $id)
    {
        $machineryPurchase = Order::find($id);
        if (!$machineryPurchase) return response()->json(['message' => 'Machinery purchase not found'], 404);
        return response()->json(new MachineryPurchaseResource($machineryPurchase));
    }

    /**
     * @OA\Put(
     *     path="/taeyoung-backend/public/api/machineryPurchase/{id}",
     *     tags={"MachineryPurchase"},
     *     summary="Update machinery purchase",
     *     description="Update a machinery purchase.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Machinery purchase ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(required=true, description="Machinery purchase data", @OA\JsonContent(ref="#/components/schemas/UpdateMachineryPurchaseRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/MachineryPurchaseResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=404, description="Machinery purchase not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Machinery purchase not found")))
     * )
     */
    public function update(UpdateMachineryPurchaseRequest $request, string $id)
    {
        $machineryPurchase = Order::find($id);
        if (!$machineryPurchase) return response()->json(['message' => 'Machinery purchase not found'], 404);

        $machineryPurchase->date = $request->input('date');
        $machineryPurchase->detail = $request->input('detail');
//        $machineryPurchase->discount = $request->input('discount');
        $machineryPurchase->currencyType = $request->input('currencyType');
        $machineryPurchase->supplier_id = $request->input('supplier_id');
        $machineryPurchase->quotation_id = $request->input('quotation_id');

        $totalMachinery = 0;
        $totalSpareParts = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpares = $request->input('detailSpareParts');

        if ($detailMachinery) {
            DetailMachinery::where('order_id', $machineryPurchase->id)->delete();
            foreach ($detailMachinery as $detail) {
                $detailMachinery = DetailMachinery::create([
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'movementType' => 'machineryPurchase',
                    'purchasePrice' => $detail['purchasePrice'],
                    'purchaseValue' => $detail['purchasePrice'] * $detail['quantity'],
                    'order_id' => $machineryPurchase->id,
                ]);
                $totalMachinery += $detailMachinery->purchasePrice * $detailMachinery->quantity;
            }
        }

        if ($detailSpares) {
            $machineryPurchase->detailSpareParts()->delete();
            $totalSpareParts = $this->addDetailSpareParts($detailSpares, $machineryPurchase);
        }

        $machineryPurchase->totalMachinery = $totalMachinery;
        $machineryPurchase->totalSpareParts = $totalSpareParts;
        $machineryPurchase->subtotal = $totalMachinery + $totalSpareParts;
        $machineryPurchase->total = $totalMachinery + $totalSpareParts;
        $machineryPurchase->totalExpense = $totalMachinery + $totalSpareParts;
        $machineryPurchase->save();

        $machineryPurchase = Order::find($machineryPurchase->id);
        return response()->json(new MachineryPurchaseResource($machineryPurchase));
    }

    /**
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/machineryPurchase/{id}",
     *     tags={"MachineryPurchase"},
     *     summary="Destroy machinery purchase",
     *     description="Destroy a machinery purchase.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Machinery purchase ID", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Machinery purchase deleted"))),
     *     @OA\Response(response=404, description="Machinery purchase not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Machinery purchase not found")))
     * )
     */
    public function destroy(string $id)
    {
        $machineryPurchase = Order::find($id);
        if (!$machineryPurchase) return response()->json(['message' => 'Machinery purchase not found'], 404);
        $machineryPurchase->delete();
        return response()->json(['message' => 'Machinery purchase deleted']);
    }

    private function addDetailSpareParts(mixed $detailSpareParts, $order)
    {
        $detailSparePartsValidate = [];
        $totalSpareParts = 0;
        foreach ($detailSpareParts as $detail) {
            if (array_key_exists($detail['spare_part_id'], $detailSparePartsValidate)) {
                $detailSparePartsValidate[$detail['spare_part_id']]['quantity'] += $detail['quantity'];
            } else {
                $detailSparePartsValidate[$detail['spare_part_id']] = $detail;
            }
        }

        foreach ($detailSparePartsValidate as $detail) {
            $sparePart = SparePart::find($detail['spare_part_id']);
            $detailSparePart = DetailSparePart::create([
                'quantity' => $detail['quantity'],
                'movementType' => 'quotation',
                'salePrice' => $sparePart->salePrice,
                'saleValue' => $sparePart->salePrice * $detail['quantity'],
                'spare_part_id' => $detail['spare_part_id'],
                'order_id' => $order->id,
            ]);
            $totalSpareParts += $detailSparePart->saleValue;
        }
        return $totalSpareParts;
    }
}
