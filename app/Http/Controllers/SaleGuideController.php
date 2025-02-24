<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexSaleGuideRequest;
use App\Http\Requests\StoreSaleGuideRequest;
use App\Http\Requests\UpdateSaleGuideRequest;
use App\Http\Resources\SaleGuideResource;
use App\Models\Currency;
use App\Models\DetailMachinery;
use App\Models\DetailSparePart;
use App\Models\MachineryInventory;
use App\Models\Order;
use App\Models\SparePartInventory;
use App\Traits\Filterable;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *   name="SaleGuide",
 *   description="API para gestionar Guías de Venta (type='guide')"
 * )
 */
class SaleGuideController extends Controller
{
    use Filterable;

    /**
     * @OA\Get(
     *   path="/taeyoung-backend/public/api/saleGuide",
     *   tags={"SaleGuide"},
     *   summary="Listar Guías de Venta",
     *   description="Retorna el listado de guías (type='guide') con filtros (fecha, número, cliente).",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="number", in="query", @OA\Schema(type="string"), description="Número de guía"),
     *   @OA\Parameter(name="date[]", in="query", @OA\Schema(type="array", @OA\Items(type="string", format="date")), description="Rango de fechas"),
     *   @OA\Parameter(name="client_id", in="query", @OA\Schema(type="integer"), description="ID del cliente"),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer"), description="Página"),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), description="Items por página"),
     *   @OA\Response(
     *       response=200,
     *       description="OK",
     *       @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SaleGuideResource"))
     *       )
     *   ),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index(IndexSaleGuideRequest $request)
    {
        $query = Order::where('type','guide');

        // Usa tu trait Filterable (si lo tienes) o la lógica que uses
        return $this->getFilteredResults(
            $query,
            $request,
            Order::filtersSale, // o defines un filtersGuide
            Order::sortSale,    // o defines un sortGuide
            SaleGuideResource::class
        );
    }

    /**
     * @OA\Post(
     *   path="/taeyoung-backend/public/api/saleGuide",
     *   tags={"SaleGuide"},
     *   summary="Crear Guía de Venta",
     *   description="Crea un registro type='guide' y disminuye stock contable.",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/StoreSaleGuideRequest")),
     *   @OA\Response(
     *       response=200,
     *       description="Guía creada",
     *       @OA\JsonContent(ref="#/components/schemas/SaleGuideResource")
     *   ),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function store(StoreSaleGuideRequest $request)
    {
        return DB::transaction(function() use($request) {

            // Si la guía requiere algún tipo de cambio (solo si vas a manejar currencyType)
            if ($request->filled('currencyType') && $request->currencyType === 'USD') {
                $exchangeRate = Currency::where('date',$request->date)->first();
                if (!$exchangeRate) {
                    return response()->json(['error'=>'No hay tipo de cambio registrado para esta fecha'],422);
                }
            }

            // 1) Crear la orden (type='guide')
            $guide = Order::create([
                'type'         => 'guide',
                'number'       => $request->number ?? 'GUIDE-'.uniqid(),
                'date'         => $request->date,
                'documentType' => $request->documentType ?? 'GUIA',
                'client_id'    => $request->client_id,
                'detail'       => $request->detail,
                'status'       => 'PENDIENTE',
                // currencyType, etc. si lo requieres
            ]);

            $subtotal = 0;

            // 2) Guardar detailMachinery
            if ($request->filled('detailMachinery')) {
                foreach ($request->detailMachinery as $item) {
                    $dm = DetailMachinery::create([
                        'machinery_id'   => $item['machinery_id'],
                        'quantity'       => $item['quantity'],
                        'movementType'   => 'guide',
                        'contablePrice'  => $item['contablePrice'] ?? 0,
                        'realPrice'      => $item['realPrice'] ?? 0,
                        'salePrice'      => 0,  // si no facturas
                        'purchasePrice'  => 0,  // ...
                        'order_id'       => $guide->id,
                        'description'    => '', // o lo que gustes
                    ]);
                    // Disminuir stock contable
                    $this->decreaseMachineryStock(
                        $item['machinery_id'],
                        $item['quantity'],
                        $dm->realPrice,
                        $dm->contablePrice
                    );
                    // Opcional: Calculas un "valor" => contablePrice * quantity
                    if (!empty($dm->contablePrice)) {
                        $subtotal += ($dm->contablePrice * $dm->quantity);
                    }
                }
            }

            // 3) Guardar detailSpareParts
            if ($request->filled('detailSpareParts')) {
                foreach ($request->detailSpareParts as $item) {
                    $sp = DetailSparePart::create([
                        'spare_part_id'  => $item['spare_part_id'],
                        'quantity'       => $item['quantity'],
                        'movementType'   => 'guide',
                        'contablePrice'  => $item['contablePrice'] ?? 0,
                        'realPrice'      => $item['realPrice'] ?? 0,
                        'salePrice'      => 0,
                        'purchasePrice'  => 0,
                        'order_id'       => $guide->id,
                    ]);
                    // Disminuir stock contable
                    $this->decreaseSparePartStock(
                        $item['spare_part_id'],
                        $item['quantity'],
                        $sp->realPrice,
                        $sp->contablePrice
                    );
                    if (!empty($sp->contablePrice)) {
                        $subtotal += ($sp->contablePrice * $sp->quantity);
                    }
                }
            }

            // 4) Actualizar totales en la guía
            $guide->subtotal = $subtotal;
            $guide->total    = $subtotal;
            $guide->save();

            return new SaleGuideResource($guide->fresh());
        });
    }

    /**
     * @OA\Get(
     *   path="/taeyoung-backend/public/api/saleGuide/{id}",
     *   tags={"SaleGuide"},
     *   summary="Mostrar Guía de Venta",
     *   description="Retorna el detalle de una guía por ID",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="ID de la guía"),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/SaleGuideResource")),
     *   @OA\Response(response=404, description="Guía no encontrada"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function show(int $id)
    {
        $guide = Order::where('type','guide')->find($id);
        if (!$guide) {
            return response()->json(['message'=>'Guide not found'],404);
        }
        return new SaleGuideResource($guide);
    }

    /**
     * @OA\Put(
     *   path="/taeyoung-backend/public/api/saleGuide/{id}",
     *   tags={"SaleGuide"},
     *   summary="Actualizar Guía de Venta",
     *   description="Edita la guía, revirtiendo el stock contable anterior y aplicando el nuevo.",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="ID de la guía"),
     *   @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/UpdateSaleGuideRequest")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/SaleGuideResource")),
     *   @OA\Response(response=404, description="Guía no encontrada"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function update(UpdateSaleGuideRequest $request, int $id)
    {
        return DB::transaction(function() use($request, $id) {

            $guide = Order::where('type','guide')->find($id);
            if (!$guide) {
                return response()->json(['message'=>'Guide not found'],404);
            }

            // 1) Revertir stock contable de los detalles antiguos
            foreach ($guide->detailMachinery as $oldMach) {
                $this->increaseMachineryStock(
                    $oldMach->machinery_id,
                    $oldMach->quantity,
                    $oldMach->realPrice     ?? 0,
                    $oldMach->contablePrice ?? 0
                );
            }
            foreach ($guide->detailSpareParts as $oldSp) {
                $this->increaseSparePartStock(
                    $oldSp->spare_part_id,
                    $oldSp->quantity,
                    $oldSp->realPrice     ?? 0,
                    $oldSp->contablePrice ?? 0
                );
            }

            // Borramos detalles
            $guide->detailMachinery()->delete();
            $guide->detailSpareParts()->delete();

            // 2) Actualizar cabecera
            $guide->update([
                'date'         => $request->input('date', $guide->date),
                'documentType' => $request->input('documentType', $guide->documentType),
                'number'       => $request->input('number', $guide->number),
                'client_id'    => $request->input('client_id', $guide->client_id),
                'detail'       => $request->input('detail', $guide->detail),
            ]);

            // 3) Insertar nuevos detalles y disminuir stock
            $subtotal = 0;

            if ($request->filled('detailMachinery')) {
                foreach ($request->detailMachinery as $item) {
                    $dm = DetailMachinery::create([
                        'machinery_id'   => $item['machinery_id'],
                        'quantity'       => $item['quantity'],
                        'movementType'   => 'guide',
                        'contablePrice'  => $item['contablePrice'] ?? 0,
                        'realPrice'      => $item['realPrice'] ?? 0,
                        'order_id'       => $guide->id,
                    ]);
                    $this->decreaseMachineryStock(
                        $dm->machinery_id,
                        $dm->quantity,
                        $dm->realPrice,
                        $dm->contablePrice
                    );
                    if ($dm->contablePrice > 0) {
                        $subtotal += ($dm->contablePrice * $dm->quantity);
                    }
                }
            }

            if ($request->filled('detailSpareParts')) {
                foreach ($request->detailSpareParts as $item) {
                    $sp = DetailSparePart::create([
                        'spare_part_id'  => $item['spare_part_id'],
                        'quantity'       => $item['quantity'],
                        'movementType'   => 'guide',
                        'contablePrice'  => $item['contablePrice'] ?? 0,
                        'realPrice'      => $item['realPrice'] ?? 0,
                        'order_id'       => $guide->id,
                    ]);
                    $this->decreaseSparePartStock(
                        $sp->spare_part_id,
                        $sp->quantity,
                        $sp->realPrice,
                        $sp->contablePrice
                    );
                    if ($sp->contablePrice > 0) {
                        $subtotal += ($sp->contablePrice * $sp->quantity);
                    }
                }
            }

            $guide->subtotal = $subtotal;
            $guide->total    = $subtotal;
            $guide->save();

            return new SaleGuideResource($guide->fresh());
        });
    }

    /**
     * @OA\Delete(
     *   path="/taeyoung-backend/public/api/saleGuide/{id}",
     *   tags={"SaleGuide"},
     *   summary="Eliminar Guía de Venta",
     *   description="Elimina la guía y revierte el stock contable de los detalles.",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="ID de la guía"),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Guide deleted"))),
     *   @OA\Response(response=404, description="No encontrado"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function destroy(int $id)
    {
        return DB::transaction(function() use($id) {

            $guide = Order::where('type','guide')->find($id);
            if (!$guide) {
                return response()->json(['message'=>'Guide not found'],404);
            }

            // Revertir stock
            foreach ($guide->detailMachinery as $oldMach) {
                $this->increaseMachineryStock(
                    $oldMach->machinery_id,
                    $oldMach->quantity,
                    $oldMach->realPrice     ?? 0,
                    $oldMach->contablePrice ?? 0
                );
            }
            foreach ($guide->detailSpareParts as $oldSp) {
                $this->increaseSparePartStock(
                    $oldSp->spare_part_id,
                    $oldSp->quantity,
                    $oldSp->realPrice     ?? 0,
                    $oldSp->contablePrice ?? 0
                );
            }

            $guide->detailMachinery()->delete();
            $guide->detailSpareParts()->delete();
            $guide->delete();

            return response()->json(['message'=>'Guide deleted']);
        });
    }

    //--------------------------------------------------------------------------
    //                 LÓGICA DE STOCK (solo contable vs real)
    //--------------------------------------------------------------------------

    private function decreaseMachineryStock(int $machineryId, int $quantity, float $realPrice, float $contablePrice)
    {
        $inventory = MachineryInventory::firstOrCreate(['machinery_id'=>$machineryId]);

        // Si realPrice > 0 => baja real_stock
        if ($realPrice > 0) {
            $inventory->real_stock = max(0, $inventory->real_stock - $quantity);
        }
        // Si contablePrice > 0 => baja contable_stock
        if ($contablePrice > 0) {
            $inventory->contable_stock = max(0, $inventory->contable_stock - $quantity);
        }

        $inventory->save();
    }

    private function decreaseSparePartStock(int $sparePartId, int $quantity, float $realPrice, float $contablePrice)
    {
        $inventory = SparePartInventory::firstOrCreate(['spare_part_id'=>$sparePartId]);

        if ($realPrice > 0) {
            $inventory->real_stock = max(0, $inventory->real_stock - $quantity);
        }
        if ($contablePrice > 0) {
            $inventory->contable_stock = max(0, $inventory->contable_stock - $quantity);
        }

        $inventory->save();
    }

    /**
     * Si anulas la guía o editas, se revierte la disminución, es decir se incrementa.
     */
    private function increaseMachineryStock(int $machineryId, int $quantity, float $realPrice, float $contablePrice)
    {
        $inventory = MachineryInventory::firstOrCreate(['machinery_id'=>$machineryId]);

        if ($realPrice > 0) {
            $inventory->real_stock += $quantity;
        }
        if ($contablePrice > 0) {
            $inventory->contable_stock += $quantity;
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
}
