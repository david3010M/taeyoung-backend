<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexSaleRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\AccountReceivable;
use App\Models\DetailMachinery;
use App\Models\DetailSparePart;
use App\Models\Order;
use App\Models\SparePart;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/sale",
     *     tags={"Sale"},
     *     summary="List Sales",
     *     description="Returns a list of Sales.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="page", name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="per_page", name="per_page", in="query", required=false, description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="documentType", name="documentType", in="query", required=false, description="Document type", @OA\Schema(type="string", enum={"BOLETA", "FACTURA"})),
     *     @OA\Parameter(parameter="number", name="number", in="query", required=false, description="Sale number", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="date", name="date[]", in="query", required=false, description="Sale date", @OA\Schema(type="array", @OA\Items(type="string", format="date"))),
     *     @OA\Parameter(parameter="client_id", name="client_id", in="query", required=false, description="Client ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="client$filterName", name="client$filterName", in="query", required=false, description="Client name", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="client$country_id", name="client$country_id", in="query", required=false, description="Client country ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="quotation_id", name="quotation_id", in="query", required=false, description="Quotation ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Client")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     * )
     */
    public function index(IndexSaleRequest $request)
    {
        return $this->getFilteredResults(
            Order::where('type', 'sale'),
            $request,
            Order::filtersSale,
            Order::sortSale,
            SaleResource::class
        );
    }

    /**
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/sale",
     *     tags={"Sale"},
     *     summary="Store Sale",
     *     description="Stores a Sale.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, description="Sale data", @OA\JsonContent(ref="#/components/schemas/StoreSaleRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/SaleResource")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StoreSaleRequest $request)
    {
        $dataSale = [
            'type' => 'sale',
            'number' => $this->nextCorrelativeQuery(Order::where('type', 'sale'), 'number'),
            'date' => $request->input('date'),
            'documentType' => $request->input('documentType'),
            'paymentType' => $request->input('paymentType'),
            'quotation_id' => $request->input('quotation_id'),
            'client_id' => $request->input('client_id'),
            'currencyType' => $request->input('currencyType', 'PEN'),
        ];

        $sale = Order::create($dataSale);
        $totalMachinery = 0;
        $totalSpareParts = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpares = $request->input('detailSpareParts');

        if ($detailMachinery) {
            foreach ($detailMachinery as $detail) {
                $detailMachinery = DetailMachinery::create([
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'movementType' => 'sale',
                    'salePrice' => $detail['salePrice'],
                    'saleValue' => $detail['salePrice'] * $detail['quantity'],
                    'order_id' => $sale->id,
                ]);
                $totalMachinery += $detailMachinery->salePrice * $detailMachinery->quantity;
            }
        }

        if ($detailSpares) {
            $totalDetailsSpareParts = $this->addDetailSpareParts($detailSpares, $sale);
            if (!$totalDetailsSpareParts['success']) {
                $sale->detailMachinery()->delete();
                $sale->delete();
                return response()->json(['error' => $totalDetailsSpareParts['message']], 422);
            }
            $totalSpareParts = $totalDetailsSpareParts["totalSpareParts"];
        }

        $sale->totalSpareParts = $totalSpareParts;
        $sale->totalMachinery = $totalMachinery;
        $sale->subtotal = $totalMachinery + $totalSpareParts;
        $sale->igv = $sale->subtotal * 0.18;
        $sale->discount = $request->input('discount', 0);
        $sale->total = $sale->subtotal + $sale->igv - $sale->discount;
        $sale->totalIncome = $sale->total;

        if ($request->input('paymentType') == 'CONTADO') {
            $sale->save();
            AccountReceivable::create([
                'days' => 0,
                'date' => $sale->date,
                'amount' => $sale->total,
                'balance' => $sale->total,
                'order_id' => $sale->id,
                'client_id' => $sale->client_id,
            ]);
        } else {
            $quotas = $request->input('quotas');
            $sumQuotas = array_sum(array_column($quotas, 'amount'));
            if ($sumQuotas != $sale->total) {
                $sale->detailMachinery()->delete();
                $sale->detailSpareParts()->delete();
                $sale->delete();
                return response()->json(['error' => 'La suma de las cuotas no coincide con el total, saldo de ' . ($sale->total - $sumQuotas)], 422);
            }
            $sale->save();

            foreach ($quotas as $quota) {
                AccountReceivable::create([
                    'days' => $quota['days'],
                    'date' => Carbon::parse($sale->date)->addDays($quota['days']),
                    'amount' => $quota['amount'],
                    'balance' => $quota['amount'],
                    'order_id' => $sale->id,
                    'client_id' => $sale->client_id,
                ]);
            }
        }

        $sale = Order::find($sale->id);
        return response()->json(new SaleResource($sale));

    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/sale/{id}",
     *     tags={"Sale"},
     *     summary="Show Sale",
     *     description="Returns a Sale.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Purchase ID", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/SaleResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Sale not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Sale not found")))
     * )
     */
    public function show(int $id)
    {
        $sale = Order::where('type', 'sale')->find($id);
        if (!$sale) return response()->json(['message' => 'Sale not found'], 404);
        return response()->json(new SaleResource($sale));
    }

    /**
     * @OA\Put(
     *     path="/taeyoung-backend/public/api/sale/{id}",
     *     tags={"Sale"},
     *     summary="Update Sale",
     *     description="Updates a Sale.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Sale ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(required=true, description="Sale data", @OA\JsonContent(ref="#/components/schemas/UpdateSaleRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/SaleResource")),
     *     @OA\Response(response=404, description="Sale not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Sale not found"))),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(UpdateSaleRequest $request, int $id)
    {
        $sale = Order::where('type', 'sale')->find($id);
        if (!$sale) return response()->json(['message' => 'Sale not found'], 404);

        $data = [
            'date' => $request->input('date', $sale->date),
            'documentType' => $request->input('documentType', $sale->documentType),
            'paymentType' => $request->input('paymentType', $sale->paymentType),
            'quotation_id' => $request->input('quotation_id', $sale->quotation_id),
            'client_id' => $request->input('client_id', $sale->client_id),
            'currencyType' => $request->input('currencyType', 'PEN'),
        ];
        $sale->update($data);

        $totalMachinery = 0;
        $totalSpareParts = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpares = $request->input('detailSpareParts');

        $sale->detailSpareParts()->delete();
        $sale->detailMachinery()->delete();

        if ($detailMachinery) {
            $sale->detailMachinery()->delete();
            foreach ($detailMachinery as $detail) {
                $detailMachinery = DetailMachinery::create([
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'movementType' => 'sale',
                    'salePrice' => $detail['salePrice'],
                    'saleValue' => $detail['salePrice'] * $detail['quantity'],
                    'order_id' => $sale->id,
                ]);
                $totalMachinery += $detailMachinery->salePrice * $detailMachinery->quantity;
            }
        }

        if ($detailSpares) {
            $totalDetailsSpareParts = $this->addDetailSpareParts($detailSpares, $sale);
            if (!$totalDetailsSpareParts['success']) {
                return response()->json(['error' => $totalDetailsSpareParts['message']], 422);
            }
            $totalSpareParts = $totalDetailsSpareParts["totalSpareParts"];
        }

        $sale->totalSpareParts = $totalSpareParts;
        $sale->totalMachinery = $totalMachinery;
        $sale->subtotal = $totalMachinery + $totalSpareParts;
        $sale->igv = $sale->subtotal * 0.18;
        $sale->discount = $request->input('discount', $sale->discount);
        $sale->total = $sale->subtotal + $sale->igv - $sale->discount;
        $sale->totalIncome = $sale->total;

        if ($request->input('paymentType') == 'CONTADO') {
            $sale->save();
            AccountReceivable::where('order_id', $sale->id)->delete();
            AccountReceivable::create([
                'days' => 0,
                'date' => $sale->date,
                'amount' => $sale->total,
                'balance' => $sale->total,
                'order_id' => $sale->id,
                'client_id' => $sale->client_id,
            ]);
        } else {
            $quotas = $request->input('quotas');
            $sumQuotas = array_sum(array_column($quotas, 'amount'));
            if ($sumQuotas != $sale->total) {
                return response()->json(['error' => 'La suma de las cuotas no coincide con el total, saldo de ' . ($sale->total - $sumQuotas)], 422);
            }
            $sale->save();
            AccountReceivable::where('order_id', $sale->id)->delete();
            foreach ($quotas as $quota) {
                AccountReceivable::create([
                    'days' => $quota['days'],
                    'date' => Carbon::parse($sale->date)->addDays($quota['days']),
                    'amount' => $quota['amount'],
                    'balance' => $quota['amount'],
                    'order_id' => $sale->id,
                    'client_id' => $sale->client_id,
                ]);
            }
        }

        $sale = Order::find($sale->id);
        return response()->json(new SaleResource($sale));
    }

    /**
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/sale/{id}",
     *     tags={"Sale"},
     *     summary="Destroy Sale",
     *     description="Deletes a Sale.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Sale ID", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Sale deleted successfully"))),
     *     @OA\Response(response=404, description="Sale not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Sale not found")))
     * )
     */
    public function destroy(int $id)
    {
        $sale = Order::where('type', 'sale')->find($id);
        if (!$sale) return response()->json(['message' => 'Sale not found'], 404);
//        $accountReceivable = AccountReceivable::where('order_id', $sale->id)->sum('balance');
//        if ($accountReceivable > 0) return response()->json(['error' => 'No se puede eliminar la venta porque tiene cuentas por cobrar pendientes'], 422);
        $sale->detailMachinery()->delete();
        $sale->detailSpareParts()->delete();
        $sale->accountReceivable()->delete();
        $sale->delete();
        return response()->json(['message' => 'Sale deleted successfully']);
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
            if ($sparePart->stock < $detail['quantity']) {
                return [
                    'success' => false,
                    'message' => 'No hay stock suficiente para el repuesto ' . $sparePart->name . ' (id: ' . $sparePart->id . ')' . ' (Stock actual: ' . $sparePart->stock . ')'
                ];
            }
            $detailSparePart = DetailSparePart::create([
                'quantity' => $detail['quantity'],
                'movementType' => 'sale',
                'salePrice' => (float)$detail['salePrice'],
                'saleValue' => (float)$detail['salePrice'] * $detail['quantity'],
                'spare_part_id' => $detail['spare_part_id'],
                'order_id' => $order->id,
            ]);
            $sparePart->stock -= $detail['quantity'];
            $totalSpareParts += $detailSparePart->saleValue;
            $sparePart->save();
        }

        return [
            'success' => true,
            'totalSpareParts' => $totalSpareParts
        ];
    }

}
