<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexPurchaseRequest;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\AccountPayable;
use App\Models\Currency;
use App\Models\DetailMachinery;
use App\Models\DetailSparePart;
use App\Models\Order;
use App\Models\SparePart;

class PurchaseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/purchase",
     *     tags={"Purchase"},
     *     summary="List Purchases",
     *     description="Returns a list of Purchases.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="page", name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="per_page", name="per_page", in="query", required=false, description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="number", name="number", in="query", required=false, description="Purchase number", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="date", name="date[]", in="query", required=false, description="Purchase date", @OA\Schema(type="array", @OA\Items(type="string", format="date"))),
     *     @OA\Parameter(parameter="supplier_id", name="supplier_id", in="query", required=false, description="Supplier ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="supplier$filterName", name="supplier$filterName", in="query", required=false, description="Supplier name", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="supplier$country_id", name="supplier$country_id", in="query", required=false, description="Supplier country ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PurchaseCollection")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     * )
     */
    public function index(IndexPurchaseRequest $request)
    {
        return $this->getFilteredResults(
            Order::where('type', 'purchase'),
            $request,
            Order::filtersPurchase,
            Order::sortPurchase,
            PurchaseResource::class
        );
    }


    /**
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/purchase",
     *     tags={"Purchase"},
     *     summary="Store Purchase",
     *     description="Store a new Purchase.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, description="Purchase data", @OA\JsonContent(ref="#/components/schemas/StorePurchaseRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PurchaseResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StorePurchaseRequest $request)
    {
        $exchangeRate = Currency::where('date', $request->date)->first();
        if ($request->input('currencyType') == 'USD') {
            if (!$exchangeRate) return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha seleccionada'], 422);
        }
        $dataPurchase = [
            'type' => 'purchase',
            'number' => $request->input('number'),
            'date' => $request->input('date'),
            'detail' => $request->input('detail'),
//            'discount' => $request->input('discount'),
            'currencyType' => $request->input('currencyType'),
            'supplier_id' => $request->input('supplier_id'),
            'quotation_id' => $request->input('quotation_id'),
        ];

        $purchase = Order::create($dataPurchase);
        $totalMachinery = 0;
        $totalSpareParts = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpares = $request->input('detailSpareParts');

        if ($detailMachinery) {
            foreach ($detailMachinery as $detail) {
                $detailMachinery = DetailMachinery::create([
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'movementType' => 'purchase',
                    'purchasePrice' => $detail['purchasePrice'],
                    'purchaseValue' => $detail['purchasePrice'] * $detail['quantity'],
                    'order_id' => $purchase->id,
                ]);
                $totalMachinery += $detailMachinery->purchasePrice * $detailMachinery->quantity;
            }
        }

        if ($detailSpares) {
            $totalSpareParts = $this->addDetailSpareParts($detailSpares, $purchase);
        }

        $purchase->totalMachinery = $totalMachinery;
        $purchase->totalSpareParts = $totalSpareParts;
        $purchase->subtotal = $totalMachinery + $totalSpareParts;
        $purchase->total = $totalMachinery + $totalSpareParts;

        $totalConvert = $purchase->currencyType == 'USD'
            ? $purchase->total // Conversión del total si está en otra moneda
            : round($purchase->total / $exchangeRate->saleRate, 2);
        $purchase->totalExpense = $totalConvert; // Refleja el ingreso final total
        $purchase->balance = $totalConvert;
        $purchase->save();

        AccountPayable::create([
            'paymentType' => 'CONTADO',
            'days' => 0,
            'date' => $purchase->date,
            'amount' => $purchase->total,
            'balance' => 0,
            'supplier_id' => $purchase->supplier_id,
            'order_id' => $purchase->id,
            'currency_id' => $exchangeRate->id,
        ]);

        $purchase = Order::find($purchase->id);
        return response()->json(new PurchaseResource($purchase));
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/purchase/{id}",
     *     tags={"Purchase"},
     *     summary="Show Purchase",
     *     description="Returns a Purchase.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Purchase ID", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PurchaseResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Purchase not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Purchase not found")))
     * )
     */
    public function show(int $id)
    {
        $purchase = Order::where('type', 'purchase')->find($id);
        if (!$purchase) return response()->json(['message' => 'Purchase not found'], 404);
        return response()->json(new PurchaseResource($purchase));
    }

    /**
     * @OA\Put(
     *     path="/taeyoung-backend/public/api/purchase/{id}",
     *     tags={"Purchase"},
     *     summary="Update Purchase",
     *     description="Update a Purchase.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Purchase ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(required=true, description="Purchase data", @OA\JsonContent(ref="#/components/schemas/UpdatePurchaseRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PurchaseResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=404, description="Purchase not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Purchase not found")))
     * )
     */
    public function update(UpdatePurchaseRequest $request, string $id)
    {
        $purchase = Order::find($id);
        if (!$purchase) return response()->json(['message' => 'Purchase not found'], 404);

        $exchangeRate = Currency::where('date', $request->date ?? $purchase->date)->first();

        if ($request->input('currencyType') == 'USD') {
            if (!$exchangeRate) return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha seleccionada'], 422);
        }
        $data = [
            'date' => $request->input('date') ?? $purchase->date,
            'detail' => $request->input('detail') ?? $purchase->detail,
            'documentType' => $request->input('documentType') ?? $purchase->documentType,
            'number' => $request->input('number') ?? $purchase->number,
            'currencyType' => $request->input('currencyType') ?? $purchase->currencyType,
            'supplier_id' => $request->input('supplier_id') ?? $purchase->supplier_id,
            'quotation_id' => $request->input('quotation_id') ?? $purchase->quotation_id,
        ];
        $purchase->update($data);

        $totalMachinery = 0;
        $totalSpareParts = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpares = $request->input('detailSpareParts');

        $purchase->detailMachinery()->delete();
        $purchase->detailSpareParts()->delete();

        if ($detailMachinery) {
            foreach ($detailMachinery as $detail) {
                $detailMachinery = DetailMachinery::create([
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'movementType' => 'purchase',
                    'purchasePrice' => $detail['purchasePrice'],
                    'purchaseValue' => $detail['purchasePrice'] * $detail['quantity'],
                    'order_id' => $purchase->id,
                ]);
                $totalMachinery += $detailMachinery->purchasePrice * $detailMachinery->quantity;
            }
        }

        if ($detailSpares) {
            $totalSpareParts = $this->addDetailSpareParts($detailSpares, $purchase);
        }

        $purchase->totalMachinery = $totalMachinery;
        $purchase->totalSpareParts = $totalSpareParts;
        $purchase->subtotal = $totalMachinery + $totalSpareParts;
        $purchase->total = $totalMachinery + $totalSpareParts;

        $totalConvert = $purchase->currencyType == 'USD'
            ? $purchase->total // Conversión del total si está en otra moneda
            : round($purchase->total / $exchangeRate->saleRate, 2);
        $purchase->totalExpense = $totalConvert; // Refleja el ingreso final total
        $purchase->balance = $totalConvert;
        $purchase->save();

        $purchase->accountPayable()->delete();
        AccountPayable::create([
            'paymentType' => 'CONTADO',
            'days' => 0,
            'date' => $purchase->date,
            'amount' => $purchase->total,
            'balance' => 0,
            'supplier_id' => $purchase->supplier_id,
            'order_id' => $purchase->id,
            'currency_id' => $exchangeRate->id,
        ]);

        $purchase = Order::find($purchase->id);
        return response()->json(new PurchaseResource($purchase));
    }

    /**
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/purchase/{id}",
     *     tags={"Purchase"},
     *     summary="Destroy Purchase",
     *     description="Destroy a Purchase.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Purchase ID", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Purchase deleted"))),
     *     @OA\Response(response=404, description="Purchase not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Purchase not found")))
     * )
     */
    public function destroy(string $id)
    {
        $purchase = Order::find($id);
        if (!$purchase) return response()->json(['message' => 'Purchase not found'], 404);
        $purchase->detailMachinery()->delete();
        $purchase->detailSpareParts()->delete();
        $purchase->accountPayable()->delete();
        $purchase->delete();
        return response()->json(['message' => 'Purchase deleted']);
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
                'purchasePrice' => (float)$detail['purchasePrice'],
                'purchaseValue' => (float)$detail['purchasePrice'] * $detail['quantity'],
                'spare_part_id' => $detail['spare_part_id'],
                'order_id' => $order->id,
            ]);

            $sparePart->stock += $detailSparePart->quantity;
            $totalSpareParts += $detailSparePart->purchaseValue;
            $sparePart->save();
        }
        return $totalSpareParts;
    }
}
