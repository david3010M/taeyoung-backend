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
use App\Models\MachineryInventory;
use App\Models\Order;
use App\Models\SparePartInventory;
use App\Traits\Filterable;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(name="Purchase", description="Módulo de compras (orders.type = 'purchase')")
 */
class PurchaseController extends Controller
{
    use Filterable;

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/purchase",
     *     tags={"Purchase"},
     *     summary="Listar Compras",
     *     description="Retorna un listado de compras (type='purchase') con filtros (proveedor, fecha, número, etc.).",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="documentType", in="query", required=false,
     *         description="Tipo de documento (BOLETA, FACTURA)",
     *         @OA\Schema(type="string", enum={"BOLETA","FACTURA"})
     *     ),
     *     @OA\Parameter(name="number", in="query", required=false,
     *         description="Número de la compra",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="date[]", in="query", required=false,
     *         description="Rango de fechas [inicio, fin]",
     *         @OA\Schema(type="array", @OA\Items(type="string", format="date"))
     *     ),
     *     @OA\Parameter(name="supplier_id", in="query", required=false,
     *         description="ID del proveedor",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(name="page", in="query", required=false,
     *         description="Número de página",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(name="per_page", in="query", required=false,
     *         description="Items por página",
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Listado de compras",
     *         @OA\JsonContent(ref="#/components/schemas/PurchaseCollection")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
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
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/purchase",
     *     tags={"Purchase"},
     *     summary="Crear nueva compra",
     *     description="Crea una compra (orders.type='purchase'), registra detalles y actualiza stock.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos de la compra",
     *         @OA\JsonContent(ref="#/components/schemas/StorePurchaseRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compra creada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/PurchaseResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(StorePurchaseRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Verificar tipo de cambio si es USD
            $exchangeRate = null;
            if ($request->currencyType === 'USD') {
                $exchangeRate = Currency::where('date', $request->date)->first();
                if (!$exchangeRate) {
                    return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha seleccionada'], 422);
                }
            }

            // Crear la orden (compra)
            $purchase = Order::create([
                'type'         => 'purchase',
                'number'       => $request->number,
                'date'         => $request->date,
                'documentType' => $request->documentType,
                'paymentType'  => $request->paymentType ?? 'CONTADO',
                'currencyType' => $request->currencyType,
                'supplier_id'  => $request->supplier_id,
                'quotation_id' => $request->quotation_id,
                'detail'       => $request->detail,
                'status'       => 'PENDIENTE',
            ]);

            $totalMachinery  = 0;
            $totalSpareParts = 0;

            // Detalle de maquinaria
            if ($request->filled('detailMachinery')) {
                foreach ($request->detailMachinery as $item) {
                    $mach = DetailMachinery::create([
                        'description'     => $item['description'] ?? '',
                        'quantity'        => $item['quantity'],
                        'movementType'    => 'purchase',
                        'purchasePrice'   => $item['purchasePrice'],
                        'purchaseValue'   => $item['purchasePrice'] * $item['quantity'],
                        'realPrice'       => $item['realPrice'] ?? 0,
                        'contablePrice'   => $item['contablePrice'] ?? 0,
                        'machinery_id'    => $item['machinery_id'],
                        'order_id'        => $purchase->id,
                    ]);
                    $totalMachinery += $mach->purchaseValue;

                    // Aumentar stock en machineries_inventory
                    $this->increaseMachineryStock(
                        $item['machinery_id'],
                        $item['quantity'],
                        $item['realPrice']     ?? 0,
                        $item['contablePrice'] ?? 0
                    );
                }
            }

            // Detalle de repuestos
            if ($request->filled('detailSpareParts')) {
                foreach ($request->detailSpareParts as $item) {
                    $sp = DetailSparePart::create([
                        'quantity'      => $item['quantity'],
                        'movementType'  => 'purchase',
                        'purchasePrice' => $item['purchasePrice'],
                        'purchaseValue' => $item['purchasePrice'] * $item['quantity'],
                        'realPrice'     => $item['realPrice'] ?? 0,
                        'contablePrice' => $item['contablePrice'] ?? 0,
                        'spare_part_id' => $item['spare_part_id'],
                        'order_id'      => $purchase->id,
                    ]);
                    $totalSpareParts += $sp->purchaseValue;

                    // Aumentar stock en spare_parts_inventory
                    $this->increaseSparePartStock(
                        $item['spare_part_id'],
                        $item['quantity'],
                        $item['realPrice']     ?? 0,
                        $item['contablePrice'] ?? 0
                    );
                }
            }

            // Calcular totales
            $purchase->totalMachinery  = $totalMachinery;
            $purchase->totalSpareParts = $totalSpareParts;
            $purchase->subtotal        = $totalMachinery + $totalSpareParts;
            $purchase->total           = $purchase->subtotal;

            // totalExpense / balance
            if ($request->currencyType === 'USD' && $exchangeRate) {
                $purchase->totalExpense = round($purchase->total * $exchangeRate->saleRate, 2);
            } else {
                $purchase->totalExpense = $purchase->total;
            }
            $purchase->balance = $purchase->totalExpense;
            $purchase->save();

            // Registrar cuenta por pagar
            AccountPayable::create([
                'paymentType' => $purchase->paymentType,
                'days'        => 0,
                'date'        => $purchase->date,
                'amount'      => $purchase->total,
                'balance'     => $purchase->paymentType === 'CONTADO' ? 0 : $purchase->total,
                'supplier_id' => $purchase->supplier_id,
                'order_id'    => $purchase->id,
                'currency_id' => $exchangeRate->id ?? null,
            ]);

            return new PurchaseResource($purchase->fresh());
        });
    }

    /**
     * @OA\Get(
     *   path="/taeyoung-backend/public/api/purchase/{id}",
     *   tags={"Purchase"},
     *   summary="Mostrar detalle de una compra",
     *   description="Obtiene la información de una compra por ID (type='purchase').",
     *   security={{"bearerAuth":{}}},
     *
     *   @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID de la compra",
     *       @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Compra encontrada",
     *       @OA\JsonContent(ref="#/components/schemas/PurchaseResource")
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Compra no encontrada",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="message", type="string", example="Purchase not found")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="No autenticado",
     *       @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *   )
     * )
     */
    public function show(int $id)
    {
        $purchase = Order::where('type','purchase')->find($id);
        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }
        return new PurchaseResource($purchase);
    }

    /**
     * @OA\Put(
     *   path="/taeyoung-backend/public/api/purchase/{id}",
     *   tags={"Purchase"},
     *   summary="Actualizar compra",
     *   description="Actualiza la compra (type='purchase'), revierte el stock anterior y suma el nuevo stock.",
     *   security={{"bearerAuth":{}}},
     *
     *   @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID de la compra",
     *       @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       description="Datos para actualizar la compra",
     *       @OA\JsonContent(ref="#/components/schemas/UpdatePurchaseRequest")
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Compra actualizada",
     *       @OA\JsonContent(ref="#/components/schemas/PurchaseResource")
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Compra no encontrada",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="message", type="string", example="Purchase not found")
     *       )
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Error de validación",
     *       @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="No autenticado",
     *       @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *   )
     * )
     */
    public function update(UpdatePurchaseRequest $request, int $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $purchase = Order::where('type','purchase')->find($id);
            if (!$purchase) {
                return response()->json(['message'=>'Purchase not found'],404);
            }

            // 1) Revertir stock anterior
            foreach ($purchase->detailMachinery as $oldMach) {
                $this->decreaseMachineryStock(
                    $oldMach->machinery_id,
                    $oldMach->quantity,
                    $oldMach->realPrice     ?? 0,
                    $oldMach->contablePrice ?? 0
                );
            }
            foreach ($purchase->detailSpareParts as $oldSp) {
                $this->decreaseSparePartStock(
                    $oldSp->spare_part_id,
                    $oldSp->quantity,
                    $oldSp->realPrice     ?? 0,
                    $oldSp->contablePrice ?? 0
                );
            }
            $purchase->detailMachinery()->delete();
            $purchase->detailSpareParts()->delete();

            // 2) Actualizar cabecera
            $exchangeRate = null;
            if ($request->currencyType === 'USD') {
                $exchangeRate = Currency::where('date', $request->date ?? $purchase->date)->first();
                if (!$exchangeRate) {
                    return response()->json(['error'=>'No se ha registrado el tipo de cambio para la fecha seleccionada'],422);
                }
            }

            $purchase->update([
                'date'         => $request->input('date', $purchase->date),
                'documentType' => $request->input('documentType', $purchase->documentType),
                'paymentType'  => $request->input('paymentType', $purchase->paymentType),
                'number'       => $request->input('number', $purchase->number),
                'currencyType' => $request->input('currencyType', $purchase->currencyType),
                'supplier_id'  => $request->input('supplier_id', $purchase->supplier_id),
                'quotation_id' => $request->input('quotation_id', $purchase->quotation_id),
                'detail'       => $request->input('detail', $purchase->detail),
            ]);

            // 3) Crear nuevos detalles y sumar stock
            $totalMachinery  = 0;
            $totalSpareParts = 0;

            if ($request->filled('detailMachinery')) {
                foreach ($request->detailMachinery as $item) {
                    $mach = DetailMachinery::create([
                        'description'   => $item['description'] ?? '',
                        'quantity'      => $item['quantity'],
                        'movementType'  => 'purchase',
                        'purchasePrice' => $item['purchasePrice'],
                        'purchaseValue' => $item['purchasePrice'] * $item['quantity'],
                        'realPrice'     => $item['realPrice'] ?? 0,
                        'contablePrice' => $item['contablePrice'] ?? 0,
                        'machinery_id'  => $item['machinery_id'],
                        'order_id'      => $purchase->id,
                    ]);
                    $totalMachinery += $mach->purchaseValue;

                    $this->increaseMachineryStock(
                        $item['machinery_id'],
                        $item['quantity'],
                        $item['realPrice']     ?? 0,
                        $item['contablePrice'] ?? 0
                    );
                }
            }

            if ($request->filled('detailSpareParts')) {
                foreach ($request->detailSpareParts as $item) {
                    $sp = DetailSparePart::create([
                        'quantity'      => $item['quantity'],
                        'movementType'  => 'purchase',
                        'purchasePrice' => $item['purchasePrice'],
                        'purchaseValue' => $item['purchasePrice'] * $item['quantity'],
                        'realPrice'     => $item['realPrice'] ?? 0,
                        'contablePrice' => $item['contablePrice'] ?? 0,
                        'spare_part_id' => $item['spare_part_id'],
                        'order_id'      => $purchase->id,
                    ]);
                    $totalSpareParts += $sp->purchaseValue;

                    $this->increaseSparePartStock(
                        $item['spare_part_id'],
                        $item['quantity'],
                        $item['realPrice']     ?? 0,
                        $item['contablePrice'] ?? 0
                    );
                }
            }

            // 4) Calcular totales
            $purchase->totalMachinery  = $totalMachinery;
            $purchase->totalSpareParts = $totalSpareParts;
            $purchase->subtotal        = $totalMachinery + $totalSpareParts;
            $purchase->total           = $purchase->subtotal;

            if ($purchase->currencyType === 'USD' && $exchangeRate) {
                $purchase->totalExpense = round($purchase->total * $exchangeRate->saleRate, 2);
            } else {
                $purchase->totalExpense = $purchase->total;
            }
            $purchase->balance = $purchase->totalExpense;
            $purchase->save();

            // 5) Actualizar cuenta por pagar
            $purchase->accountPayable()->delete();
            AccountPayable::create([
                'paymentType' => $purchase->paymentType,
                'days'        => 0,
                'date'        => $purchase->date,
                'amount'      => $purchase->total,
                'balance'     => $purchase->paymentType === 'CONTADO' ? 0 : $purchase->total,
                'supplier_id' => $purchase->supplier_id,
                'order_id'    => $purchase->id,
                'currency_id' => $exchangeRate->id ?? null,
            ]);

            return new PurchaseResource($purchase->fresh());
        });
    }

    /**
     * @OA\Delete(
     *   path="/taeyoung-backend/public/api/purchase/{id}",
     *   tags={"Purchase"},
     *   summary="Eliminar compra",
     *   description="Elimina la compra y revierte el stock de sus detalles.",
     *   security={{"bearerAuth":{}}},
     *
     *   @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID de la compra",
     *       @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Compra eliminada",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="message", type="string", example="Purchase deleted")
     *       )
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Compra no encontrada",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="message", type="string", example="Purchase not found")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="No autenticado",
     *       @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *   )
     * )
     */
    public function destroy(int $id)
    {
        return DB::transaction(function () use ($id) {
            $purchase = Order::where('type','purchase')->find($id);
            if (!$purchase) {
                return response()->json(['message'=>'Purchase not found'],404);
            }
            // Revertir stock
            foreach ($purchase->detailMachinery as $oldMach) {
                $this->decreaseMachineryStock(
                    $oldMach->machinery_id,
                    $oldMach->quantity,
                    $oldMach->realPrice     ?? 0,
                    $oldMach->contablePrice ?? 0
                );
            }
            foreach ($purchase->detailSpareParts as $oldSp) {
                $this->decreaseSparePartStock(
                    $oldSp->spare_part_id,
                    $oldSp->quantity,
                    $oldSp->realPrice     ?? 0,
                    $oldSp->contablePrice ?? 0
                );
            }
            $purchase->detailMachinery()->delete();
            $purchase->detailSpareParts()->delete();
            $purchase->accountPayable()->delete();
            $purchase->delete();

            return response()->json(['message' => 'Purchase deleted']);
        });
    }

    //--------------------------------------------------------------------------
    //                           LÓGICA DE STOCK
    //--------------------------------------------------------------------------

    private function increaseMachineryStock(int $machineryId, int $quantity, float $realPrice, float $contablePrice)
    {
        $inventory = MachineryInventory::firstOrCreate(['machinery_id' => $machineryId]);

        // Si realPrice > 0, sumamos al real_stock
        if ($realPrice > 0) {
            $inventory->real_stock += $quantity;
        }
        // Si contablePrice > 0, sumamos al contable_stock
        if ($contablePrice > 0) {
            $inventory->contable_stock += $quantity;
        }
        $inventory->save();
    }

    private function decreaseMachineryStock(int $machineryId, int $quantity, float $realPrice, float $contablePrice)
    {
        $inventory = MachineryInventory::where('machinery_id',$machineryId)->first();
        if (!$inventory) {
            return; // No existe inventario
        }
        if ($realPrice > 0) {
            $inventory->real_stock = max(0, $inventory->real_stock - $quantity);
        }
        if ($contablePrice > 0) {
            $inventory->contable_stock = max(0, $inventory->contable_stock - $quantity);
        }
        $inventory->save();
    }

    private function increaseSparePartStock(int $sparePartId, int $quantity, float $realPrice, float $contablePrice)
    {
        $inventory = SparePartInventory::firstOrCreate(['spare_part_id'=>$sparePartId]);

        if ($realPrice > 0) {
            $inventory->real_stock += $quantity;
        }
        if ($contablePrice > 0) {
            $inventory->contable_stock += $quantity;
        }
        $inventory->save();
    }

    private function decreaseSparePartStock(int $sparePartId, int $quantity, float $realPrice, float $contablePrice)
    {
        $inventory = SparePartInventory::where('spare_part_id',$sparePartId)->first();
        if (!$inventory) {
            return;
        }
        if ($realPrice > 0) {
            $inventory->real_stock = max(0, $inventory->real_stock - $quantity);
        }
        if ($contablePrice > 0) {
            $inventory->contable_stock = max(0, $inventory->contable_stock - $quantity);
        }
        $inventory->save();
    }
}
