<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarehouseDocumentRequest;
use App\Http\Requests\UpdateWarehouseDocumentRequest;
use App\Http\Resources\WarehouseDocumentResource;
use App\Models\WarehouseDocument;
use App\Models\WarehouseDocumentDetail;
use App\Models\MachineryInventory;
use App\Models\SparePartInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Documentos de Almacén (Ingreso/Salida, Real/Contable).
 */
class WarehouseDocumentController extends Controller
{
    /**
     * @OA\Get(
     *   path="/taeyoung-backend/public/api/warehouseDocument",
     *   tags={"WarehouseDocument"},
     *   summary="Listar documentos de almacén",
     *   description="Obtiene la lista de documentos de almacén con filtros opcionales",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer"), description="Número de página"),
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer"), description="Items por página"),
     *   @OA\Parameter(name="documentType", in="query", required=false, @OA\Schema(type="string", enum={"ingreso","salida"}), description="Filtrar por tipo de documento"),
     *   @OA\Parameter(name="mode", in="query", required=false, @OA\Schema(type="string", enum={"Real","Contable"}), description="Filtrar por modo"),
     *   @OA\Parameter(name="number", in="query", required=false, @OA\Schema(type="string"), description="Filtrar por número"),
     *   @OA\Parameter(
     *     name="date[]",
     *     in="query",
     *     required=false,
     *     description="Rango de fechas [date[0], date[1]]",
     *     @OA\Schema(type="array", @OA\Items(type="string", format="date"))
     *   ),
     *   @OA\Response(response=200, description="Lista de documentos", @OA\JsonContent(type="object")),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index(Request $request)
    {
        $query = WarehouseDocument::query();

        if ($request->filled('documentType')) {
            $query->where('documentType', $request->documentType);
        }
        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }
        if ($request->filled('number')) {
            $query->where('number','like','%'.$request->number.'%');
        }
        if (is_array($request->date) && count($request->date) === 2) {
            $query->whereBetween('date', [$request->date[0], $request->date[1]]);
        }

        $docs = $query->with('details')->orderBy('id','desc')->paginate($request->get('per_page', 10));
        return WarehouseDocumentResource::collection($docs);
    }

    /**
     * @OA\Post(
     *   path="/taeyoung-backend/public/api/warehouseDocument",
     *   tags={"WarehouseDocument"},
     *   summary="Crear documento de almacén",
     *   description="Crea un documento de almacén (ingreso o salida, real o contable)",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/StoreWarehouseDocumentRequest")
     *   ),
     *   @OA\Response(response=200, description="Documento creado", @OA\JsonContent(ref="#/components/schemas/WarehouseDocumentResource")),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function store(StoreWarehouseDocumentRequest $request)
    {
        return DB::transaction(function() use ($request) {

            // Generar número si no viene:
            $number = $request->number;
            if (!$number) {
                $number = $this->generateDocumentNumber();
            }

            $doc = WarehouseDocument::create([
                'date'         => $request->date,
                'number'       => $number,
                'documentType' => $request->documentType,
                'mode'         => $request->mode,
                'reason'       => $request->reason,
                'comment'      => $request->comment,
            ]);

            // Unificar detalles repetidos (opcional):
            $unifiedDetails = $this->unifyDetails($request->details);

            // Crear detalles y actualizar stock:
            foreach ($unifiedDetails as $detail) {
                $wdDetail = WarehouseDocumentDetail::create([
                    'warehouse_document_id' => $doc->id,
                    'machinery_id'          => $detail['machinery_id'] ?? null,
                    'spare_part_id'         => $detail['spare_part_id'] ?? null,
                    'quantity'              => $detail['quantity'],
                ]);

                $this->updateInventory(
                    $doc->documentType,
                    $doc->mode,
                    $detail['machinery_id'] ?? null,
                    $detail['spare_part_id'] ?? null,
                    $detail['quantity']
                );
            }

            $doc->load('details');
            return new WarehouseDocumentResource($doc);
        });
    }

    /**
     * @OA\Get(
     *   path="/taeyoung-backend/public/api/warehouseDocument/{id}",
     *   tags={"WarehouseDocument"},
     *   summary="Mostrar documento de almacén",
     *   description="Muestra un documento de almacén por ID",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID del documento", @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Documento encontrado", @OA\JsonContent(ref="#/components/schemas/WarehouseDocumentResource")),
     *   @OA\Response(response=404, description="No encontrado"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function show($id)
    {
        $doc = WarehouseDocument::with('details')->findOrFail($id);
        return new WarehouseDocumentResource($doc);
    }

    /**
     * @OA\Put(
     *   path="/taeyoung-backend/public/api/warehouseDocument/{id}",
     *   tags={"WarehouseDocument"},
     *   summary="Actualizar documento de almacén",
     *   description="Actualiza un documento de almacén, revirtiendo el stock anterior y aplicando el nuevo",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID del documento", @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/UpdateWarehouseDocumentRequest")
     *   ),
     *   @OA\Response(response=200, description="Documento actualizado", @OA\JsonContent(ref="#/components/schemas/WarehouseDocumentResource")),
     *   @OA\Response(response=404, description="No encontrado"),
     *   @OA\Response(response=422, description="Datos inválidos"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function update(UpdateWarehouseDocumentRequest $request, $id)
    {
        return DB::transaction(function() use ($request, $id) {
            $doc = WarehouseDocument::with('details')->findOrFail($id);

            // 1) Revertir stock de los detalles antiguos
            foreach ($doc->details as $oldDetail) {
                $this->updateInventory(
                    $doc->documentType,
                    $doc->mode,
                    $oldDetail->machinery_id,
                    $oldDetail->spare_part_id,
                    $oldDetail->quantity,
                    true // true => revertir
                );
            }

            // 2) Borrar detalles antiguos
            WarehouseDocumentDetail::where('warehouse_document_id', $doc->id)->delete();

            // 3) Actualizar campos del documento
            $number = $request->number;
            if (!$number) {
                $number = $doc->number; // o vuelve a generar si lo prefieres
            }

            $doc->update([
                'date'         => $request->date,
                'number'       => $number,
                'documentType' => $request->documentType,
                'mode'         => $request->mode,
                'reason'       => $request->reason,
                'comment'      => $request->comment,
            ]);

            // 4) Unificar y crear nuevos detalles, actualizar stock
            $unifiedDetails = $this->unifyDetails($request->details);
            foreach ($unifiedDetails as $detail) {
                WarehouseDocumentDetail::create([
                    'warehouse_document_id' => $doc->id,
                    'machinery_id'          => $detail['machinery_id'] ?? null,
                    'spare_part_id'         => $detail['spare_part_id'] ?? null,
                    'quantity'              => $detail['quantity'],
                ]);

                $this->updateInventory(
                    $doc->documentType,
                    $doc->mode,
                    $detail['machinery_id'] ?? null,
                    $detail['spare_part_id'] ?? null,
                    $detail['quantity']
                );
            }

            $doc->load('details');
            return new WarehouseDocumentResource($doc);
        });
    }

    /**
     * @OA\Delete(
     *   path="/taeyoung-backend/public/api/warehouseDocument/{id}",
     *   tags={"WarehouseDocument"},
     *   summary="Eliminar documento de almacén",
     *   description="Elimina un documento de almacén y revierte su stock",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID del documento", @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Documento eliminado", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Documento eliminado"))),
     *   @OA\Response(response=404, description="No encontrado"),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function destroy($id)
    {
        return DB::transaction(function() use ($id) {
            $doc = WarehouseDocument::with('details')->findOrFail($id);

            // Revertir stock antes de eliminar
            foreach ($doc->details as $oldDetail) {
                $this->updateInventory(
                    $doc->documentType,
                    $doc->mode,
                    $oldDetail->machinery_id,
                    $oldDetail->spare_part_id,
                    $oldDetail->quantity,
                    true // revertir
                );
            }

            $doc->details()->delete();
            $doc->delete();

            return response()->json(['message' => 'Documento eliminado']);
        });
    }

    /**
     * Unifica líneas repetidas (mismo machinery_id o spare_part_id).
     */
    private function unifyDetails(array $details): array
    {
        $unified = [];
        foreach ($details as $d) {
            // Definimos clave (machinery vs. spare):
            if (!empty($d['machinery_id'])) {
                $key = 'M'.$d['machinery_id'];
            } else {
                $key = 'S'.$d['spare_part_id'];
            }
            if (isset($unified[$key])) {
                $unified[$key]['quantity'] += $d['quantity'];
            } else {
                $unified[$key] = $d;
            }
        }
        return array_values($unified);
    }

    /**
     * Actualiza el inventario (real o contable) según documentType y mode.
     * Si $revert=true, se invierte la operación (resta en caso de ingreso, suma en caso de salida).
     */
    private function updateInventory(string $documentType, string $mode, ?int $machineryId, ?int $sparePartId, int $quantity, bool $revert=false)
    {
        // "ingreso" => stock += quantity
        // "salida"  => stock -= quantity
        // si $revert => lo contrario
        $mult = 1;
        if ($documentType === 'salida') {
            $mult = -1;
        }
        if ($revert) {
            $mult = $mult * -1; // invierte la operación
        }

        if ($machineryId) {
            $inv = MachineryInventory::firstOrCreate(['machinery_id' => $machineryId]);
            if ($mode === 'Real') {
                $inv->real_stock += ($quantity * $mult);
            } else {
                $inv->contable_stock += ($quantity * $mult);
            }
            $inv->save();
        } else if ($sparePartId) {
            $inv = SparePartInventory::firstOrCreate(['spare_part_id' => $sparePartId]);
            if ($mode === 'Real') {
                $inv->real_stock += ($quantity * $mult);
            } else {
                $inv->contable_stock += ($quantity * $mult);
            }
            $inv->save();
        }
    }

    /**
     * Genera el número siguiente. Ajusta según tu lógica/BD.
     */
    private function generateDocumentNumber(): string
    {
        // Ejemplo simple: contar la cantidad de docs + 1
        $count = WarehouseDocument::count() + 1;
        return 'DOC-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
