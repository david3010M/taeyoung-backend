<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexInventoryRequest;
use App\Http\Resources\MachineryInventoryResource;
use App\Http\Resources\SparePartInventoryResource;
use App\Models\MachineryInventory;
use App\Models\SparePartInventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/inventory/machineries",
     *     tags={"Inventory"},
     *     summary="Lista el inventario de maquinarias",
     *     description="Retorna la paginación del inventario de maquinarias (real_stock, contable_stock) y muestra también el último realPrice/contablePrice proveniente de detail_machineries.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items por página",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Columna para ordenar (ej: id)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="direction",
     *         in="query",
     *         description="Dirección de orden (asc o desc)",
     *         @OA\Schema(type="string", enum={"asc","desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de inventario de maquinarias",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/MachineryInventoryResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="No autenticado"),
     * )
     */
    public function indexMachineries(IndexInventoryRequest $request)
    {
        // Cargamos la relación 'machinery' y 'latestPurchaseDetail'
        $query = MachineryInventory::with(['machinery.latestPurchaseDetail']);

        // Orden
        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        }

        $perPage = $request->get('per_page', 15);
        $inventories = $query->paginate($perPage);

        return MachineryInventoryResource::collection($inventories);
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/inventory/spare-parts",
     *     tags={"Inventory"},
     *     summary="Lista el inventario de repuestos",
     *     description="Retorna la paginación del inventario de repuestos (real_stock, contable_stock) y muestra también el último realPrice/contablePrice proveniente de detail_spare_parts.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items por página",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Columna para ordenar (ej: id)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="direction",
     *         in="query",
     *         description="Dirección de orden (asc o desc)",
     *         @OA\Schema(type="string", enum={"asc","desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de inventario de repuestos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/SparePartInventoryResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="No autenticado"),
     * )
     */
    public function indexSpareParts(IndexInventoryRequest $request)
    {
        $query = SparePartInventory::with(['sparePart.latestPurchaseDetail']);

        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        }

        $perPage = $request->get('per_page', 15);
        $inventories = $query->paginate($perPage);

        return SparePartInventoryResource::collection($inventories);
    }
}
