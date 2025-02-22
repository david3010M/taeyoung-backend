<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexSaleRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\AccountReceivable;
use App\Models\Currency;
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
     *     @OA\Parameter(parameter="status", name="status", in="query", required=false, description="Status", @OA\Schema(type="string", enum={"PENDIENTE", "PAGADO", "VENCIDO"})),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Client")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexSaleRequest $request)
    {
        // Filtra, ordena y pagina usando tu método getFilteredResults
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
        // Verifica tipo de cambio si currencyType = USD
        $exchangeRate = Currency::where('date', $request->date)->first();
        if ($request->input('currencyType') === 'USD' && !$exchangeRate) {
            return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha seleccionada'], 422);
        }

        $igvActive = (bool)$request->igvActive;
        $dataSale = [
            'type'         => 'sale',
            'number'       => $this->nextCorrelativeQuery(Order::where('type', 'sale'), 'number'),
            'date'         => $request->input('date'),
            'documentType' => $request->input('documentType'),
            'paymentType'  => $request->input('paymentType'),
            'quotation_id' => $request->input('quotation_id'),
            'client_id'    => $request->input('client_id'),
            'currencyType' => $request->input('currencyType'),
            'igvActive'    => $igvActive,
        ];

        // Crea la venta (Order con type=sale)
        $sale = Order::create($dataSale);

        $totalMachinery  = 0;
        $totalSpareParts = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpares    = $request->input('detailSpareParts');

        // Crea los detalles de maquinaria
        if ($detailMachinery) {
            foreach ($detailMachinery as $detail) {
                DetailMachinery::create([
                    'machinery_id'   => $detail['machinery_id'],
                    'quantity'       => $detail['quantity'],
                    'movementType'   => 'sale',
                    'salePrice'      => $detail['salePrice'],
                    'saleValue'      => $detail['salePrice'] * $detail['quantity'],
                    'realPrice'      => $detail['realPrice'] ?? null,
                    'contablePrice'  => $detail['contablePrice'] ?? null,
                    'order_id'       => $sale->id,
                ]);
                $totalMachinery += $detail['salePrice'] * $detail['quantity'];
            }
        }

        // Crea los detalles de repuestos
        if ($detailSpares) {
            $totalDetailsSpareParts = $this->addDetailSpareParts($detailSpares, $sale);
            if (!$totalDetailsSpareParts['success']) {
                // Si falla (stock, etc.), revertimos
                $sale->detailMachinery()->delete();
                $sale->delete();
                return response()->json(['error' => $totalDetailsSpareParts['message']], 422);
            }
            $totalSpareParts = $totalDetailsSpareParts['totalSpareParts'];
        }

        // Calcula totales
        $sale->totalSpareParts = $totalSpareParts;
        $sale->totalMachinery  = $totalMachinery;
        $sale->discount        = $request->input('discount', 0);
        $sale->subtotal        = $totalMachinery + $totalSpareParts - $sale->discount;
        $sale->igv             = $igvActive ? round($sale->subtotal * 0.18, 2) : 0;
        $sale->total           = $sale->subtotal + $sale->igv;

        // Convierte total si es USD
        if ($sale->currencyType === 'USD') {
            $totalConvert = round($sale->total * $exchangeRate->buyRate, 2);
        } else {
            $totalConvert = $sale->total;
        }
        $sale->totalIncome = $totalConvert;
        $sale->balance     = $totalConvert;

        // Crea cuentas por cobrar (contado o crédito)
        if ($request->input('paymentType') === 'CONTADO') {
            $sale->save();
            AccountReceivable::create([
                'paymentType' => 'CONTADO',
                'days'        => 0,
                'date'        => $sale->date,
                'amount'      => $sale->total,
                'balance'     => $sale->total,
                'order_id'    => $sale->id,
                'client_id'   => $sale->client_id,
                'currency_id' => $exchangeRate ? $exchangeRate->id : null,
            ]);
        } else {
            // CREDITO
            $quotas    = $request->input('quotas');
            $sumQuotas = array_sum(array_column($quotas, 'amount'));

            if (round($sale->total, 2) !== round($sumQuotas, 2)) {
                // Revertir si no coincide
                $sale->detailMachinery()->delete();
                $sale->detailSpareParts()->delete();
                $sale->delete();
                return response()->json([
                    'error' => 'La suma de las cuotas no coincide con el total, saldo de ' . ($sale->total - $sumQuotas)
                ], 422);
            }
            $sale->save();

            foreach ($quotas as $quota) {
                AccountReceivable::create([
                    'paymentType' => 'CREDITO',
                    'days'        => $quota['days'],
                    'date'        => Carbon::parse($sale->date)->addDays($quota['days']),
                    'amount'      => $quota['amount'],
                    'balance'     => $quota['amount'],
                    'order_id'    => $sale->id,
                    'client_id'   => $sale->client_id,
                    'currency_id' => $exchangeRate ? $exchangeRate->id : null,
                ]);
            }
        }

        // Retornar la venta con su resource
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
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Sale ID", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/SaleResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Sale not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Sale not found")))
     * )
     */
    public function show(int $id)
    {
        $sale = Order::where('type', 'sale')->find($id);
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }
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
        // STATUS: PENDIENTE, PAGANDO, PAGADO, VENCIDO
        $sale = Order::where('type', 'sale')
            ->where('status', 'PENDIENTE')
            ->find($id);

        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        // Verifica tipo de cambio si currencyType = USD
        $exchangeRate = Currency::where('date', $request->date ?? $sale->date)->first();
        if ($request->input('currencyType') === 'USD' && !$exchangeRate) {
            return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha seleccionada'], 422);
        }

        $igvActive = (bool)($request->igvActive ?? $sale->igvActive);

        // Actualizar datos principales de la venta
        $data = [
            'date'         => $request->input('date', $sale->date),
            'documentType' => $request->input('documentType', $sale->documentType),
            'paymentType'  => $request->input('paymentType', $sale->paymentType),
            'quotation_id' => $request->input('quotation_id', $sale->quotation_id),
            'client_id'    => $request->input('client_id', $sale->client_id),
            'currencyType' => $request->input('currencyType', 'PEN'),
            'igvActive'    => $igvActive,
        ];
        $sale->update($data);

        // Borrar detalle anterior
        $sale->detailSpareParts()->delete();
        $sale->detailMachinery()->delete();
        $sale->accountReceivable()->delete();

        $totalMachinery  = 0;
        $totalSpareParts = 0;

        // detailMachinery
        if ($request->filled('detailMachinery')) {
            foreach ($request->detailMachinery as $detail) {
                DetailMachinery::create([
                    'machinery_id'   => $detail['machinery_id'],
                    'quantity'       => $detail['quantity'],
                    'movementType'   => 'sale',
                    'salePrice'      => $detail['salePrice'],
                    'saleValue'      => $detail['salePrice'] * $detail['quantity'],
                    'realPrice'      => $detail['realPrice'] ?? null,
                    'contablePrice'  => $detail['contablePrice'] ?? null,
                    'order_id'       => $sale->id,
                ]);
                $totalMachinery += $detail['salePrice'] * $detail['quantity'];
            }
        }

        // detailSpareParts
        if ($request->filled('detailSpareParts')) {
            $totalDetailsSpareParts = $this->addDetailSpareParts($request->detailSpareParts, $sale);
            if (!$totalDetailsSpareParts['success']) {
                return response()->json(['error' => $totalDetailsSpareParts['message']], 422);
            }
            $totalSpareParts = $totalDetailsSpareParts['totalSpareParts'];
        }

        // Calcula totales
        $sale->totalSpareParts = $totalSpareParts;
        $sale->totalMachinery  = $totalMachinery;
        $sale->discount        = $request->input('discount', $sale->discount);
        $sale->subtotal        = $totalMachinery + $totalSpareParts - $sale->discount;
        $sale->igv             = $igvActive ? round($sale->subtotal * 0.18, 2) : 0;
        $sale->total           = $sale->subtotal + $sale->igv;

        if ($sale->currencyType === 'USD' && $exchangeRate) {
            $totalConvert = round($sale->total * $exchangeRate->buyRate, 2);
        } else {
            $totalConvert = $sale->total;
        }
        $sale->totalIncome = $totalConvert;
        $sale->balance     = $totalConvert;

        // Manejo de cuotas (CONTADO o CREDITO)
        if ($request->input('paymentType') === 'CONTADO') {
            $sale->save();
            // Crea una sola cuenta por cobrar
            AccountReceivable::create([
                'days'      => 0,
                'date'      => $sale->date,
                'amount'    => $sale->total,
                'balance'   => $sale->total,
                'order_id'  => $sale->id,
                'client_id' => $sale->client_id,
            ]);
        } else {
            // CREDITO
            $quotas    = $request->input('quotas');
            $sumQuotas = array_sum(array_column($quotas, 'amount'));

            if (round($sale->total, 2) !== round($sumQuotas, 2)) {
                return response()->json([
                    'error' => 'La suma de las cuotas no coincide con el total, saldo de ' . ($sale->total - $sumQuotas)
                ], 422);
            }
            $sale->save();

            foreach ($quotas as $quota) {
                AccountReceivable::create([
                    'days'      => $quota['days'],
                    'date'      => Carbon::parse($sale->date)->addDays($quota['days']),
                    'amount'    => $quota['amount'],
                    'balance'   => $quota['amount'],
                    'order_id'  => $sale->id,
                    'client_id' => $sale->client_id,
                ]);
            }
        }

        // (Se ha quitado el bloque que disminuye stock en repuestos)

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
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        $sale->detailMachinery()->delete();
        $sale->detailSpareParts()->delete();
        $sale->accountReceivable()->delete();
        $sale->delete();

        return response()->json(['message' => 'Sale deleted successfully']);
    }

    /**
     * Crea los DetailSparePart asociados a la venta, validando stock o cualquier otra lógica.
     * Devuelve ['success' => bool, 'totalSpareParts' => float, 'message' => string?].
     */
    private function addDetailSpareParts(mixed $detailSpareParts, Order $order): array
    {
        $detailSparePartsValidate = [];
        $totalSpareParts = 0;

        // Agrupar items repetidos (si se desea)
        foreach ($detailSpareParts as $detail) {
            if (array_key_exists($detail['spare_part_id'], $detailSparePartsValidate)) {
                $detailSparePartsValidate[$detail['spare_part_id']]['quantity'] += $detail['quantity'];
            } else {
                $detailSparePartsValidate[$detail['spare_part_id']] = $detail;
            }
        }

        foreach ($detailSparePartsValidate as $detail) {
            $sparePart = SparePart::find($detail['spare_part_id']);
            // Aquí podrías validar stock, etc.
            // if ($sparePart->stock < $detail['quantity']) {
            //     return [
            //         'success' => false,
            //         'message' => 'No hay stock suficiente para el repuesto ' . $sparePart->name
            //     ];
            // }

            $dsp = DetailSparePart::create([
                'quantity'       => $detail['quantity'],
                'movementType'   => 'sale',
                'salePrice'      => (float)$detail['salePrice'],
                'saleValue'      => (float)$detail['salePrice'] * $detail['quantity'],
                'realPrice'      => $detail['realPrice'] ?? null,
                'contablePrice'  => $detail['contablePrice'] ?? null,
                'spare_part_id'  => $detail['spare_part_id'],
                'order_id'       => $order->id,
            ]);
            $totalSpareParts += $dsp->saleValue;

            // Si no quieres disminuir stock todavía, no lo hagas
            // $sparePart->stock -= $detail['quantity'];
            // $sparePart->save();
        }

        return [
            'success'         => true,
            'totalSpareParts' => $totalSpareParts,
        ];
    }
}
