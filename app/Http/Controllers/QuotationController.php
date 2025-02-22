<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Http\Requests\IndexQuotationRequest;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Http\Resources\QuotationResource;
use App\Models\Currency;
use App\Models\DetailMachinery;
use App\Models\DetailSparePart;
use App\Models\File;
use App\Models\Quotation;
use App\Traits\Filterable;

class QuotationController extends Controller
{
    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/quotation",
     *     tags={"Quotation"},
     *     summary="Get all quotations",
     *     description="Get all quotations",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         parameter="all",
     *         name="all",
     *         in="query",
     *         required=false,
     *         description="Get all quotations without pagination",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         parameter="page",
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         parameter="per_page",
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         parameter="sort",
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Sort by column",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         parameter="direction",
     *         name="direction",
     *         in="query",
     *         required=false,
     *         description="Sort direction",
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         parameter="number",
     *         name="number",
     *         in="query",
     *         required=false,
     *         description="Filter by quotation number",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         parameter="date",
     *         name="date[]",
     *         in="query",
     *         required=false,
     *         description="Filter by date range",
     *         @OA\Schema(type="array", @OA\Items(type="string"))
     *     ),
     *     @OA\Parameter(
     *         parameter="detail",
     *         name="detail",
     *         in="query",
     *         required=false,
     *         description="Filter by detail",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         parameter="client$filterName",
     *         name="client$filterName",
     *         in="query",
     *         required=false,
     *         description="Filter by client name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         parameter="status",
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by status (Pendiente or Finalizado)",
     *         @OA\Schema(type="string", enum={"Pendiente", "Finalizado"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PurchaseCollection")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function index(IndexQuotationRequest $request)
    {
        $query = Quotation::query();

        // Filtro manual por status (Pendiente, Finalizado)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $this->getFilteredResults(
            $query,             
            $request,
            Quotation::filters,
            Quotation::sorts,
            QuotationResource::class
        );
    }

    /**
     * @OA\Post(
     *     path="/taeyoung-backend/public/api/quotation",
     *     tags={"Quotation"},
     *     summary="Store a new quotation",
     *     description="Store a new quotation",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/StoreQuotationRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/QuotationResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *     )
     * )
     */
    public function store(StoreQuotationRequest $request)
    {
        // Verificar tipo de cambio si currencyType = USD
        $exchangeRate = Currency::where('date', $request->date)->first();
        if ($request->input('currencyType') === 'USD' && !$exchangeRate) {
            return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha seleccionada'], 422);
        }

        $igvActive = (bool)$request->igvActive;
        // Si no llega status, por defecto 'Pendiente'
        $status = $request->input('status') ?? 'Pendiente';

        $dataQuotation = [
            'number'       => $this->nextCorrelative(Quotation::class, 'number'),
            'date'         => $request->input('date'),
            'detail'       => $request->input('detail'),
            'currencyType' => $request->input('currencyType'),
            'discount'     => $request->input('discount', 0),
            'client_id'    => $request->input('client_id'),
            'igvActive'    => $igvActive,
            'status'       => $status,
        ];

        $quotation = Quotation::create($dataQuotation);

        // Calcular totales
        $totalMachinery  = 0;
        $totalSpareParts = 0;

        // DETALLES DE MAQUINARIA
        if ($request->filled('detailMachinery')) {
            $totalMachinery = $this->addDetailMachinery($request->detailMachinery, $quotation);
        }

        // DETALLES DE REPUESTOS
        if ($request->filled('detailSpareParts')) {
            $totalSpareParts = $this->addDetailSpareParts($request->detailSpareParts, $quotation);
        }

        // Calcular totales finales
        $quotation->totalMachinery  = $totalMachinery;
        $quotation->totalSpareParts = $totalSpareParts;
        $quotation->discount        = (float)$request->input('discount', 0);
        $quotation->subtotal        = $totalMachinery + $totalSpareParts - $quotation->discount;
        $quotation->igv             = $igvActive ? round($quotation->subtotal * 0.18, 2) : 0;
        $quotation->total           = $quotation->subtotal + $quotation->igv;
        $quotation->save();

        $quotation->refresh();
        return response()->json(new QuotationResource($quotation));
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/quotation/{id}",
     *     tags={"Quotation"},
     *     summary="Show a quotation",
     *     description="Show a quotation",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         parameter="id",
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Quotation ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/QuotationResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Quotation not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Quotation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *     )
     * )
     */
    public function show(int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) {
            return response()->json(['message' => 'Quotation not found'], 404);
        }
        return response()->json(new QuotationResource($quotation));
    }

/**
 * @OA\Put(
 *     path="/taeyoung-backend/public/api/quotation/{id}",
 *     tags={"Quotation"},
 *     summary="Update a quotation",
 *     description="Update a quotation",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         parameter="id",
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Quotation ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Quotation data",
 *         @OA\JsonContent(
 *             required={"date", "detail", "currencyType", "client_id"},
 *             @OA\Property(property="date", type="string", format="date", example="2024-08-19"),
 *             @OA\Property(property="detail", type="string", example="This is a detail"),
 *             @OA\Property(property="discount", type="number", example="0"),
 *             @OA\Property(property="currencyType", type="string", example="USD"),
 *             @OA\Property(property="client_id", type="integer", example="21"),
 *             @OA\Property(property="igvActive", type="boolean", example=true),
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 enum={"Pendiente","Finalizado"},
 *                 example="Pendiente"
 *             ),
 *             @OA\Property(
 *                 property="detailMachinery",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="description", type="string", example="Excavadora X"),
 *                     @OA\Property(property="quantity", type="integer", example=1),
 *                     @OA\Property(property="purchasePrice", type="number", format="float", example=100),
 *                     @OA\Property(property="salePrice", type="number", format="float", example=150),
 *                     @OA\Property(property="realPrice", type="number", format="float", example=120),
 *                     @OA\Property(property="contablePrice", type="number", format="float", example=130),
 *                     @OA\Property(property="machinery_id", type="integer", example=1)
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="detailSpareParts",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="quantity", type="number", example=1),
 *                     @OA\Property(property="purchasePrice", type="number", format="float", example=100),
 *                     @OA\Property(property="salePrice", type="number", format="float", example=150),
 *                     @OA\Property(property="realPrice", type="number", format="float", example=120),
 *                     @OA\Property(property="contablePrice", type="number", format="float", example=130),
 *                     @OA\Property(property="spare_part_id", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/QuotationResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Quotation not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Quotation not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
 *     )
 * )
 */
    public function update(UpdateQuotationRequest $request, int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) {
            return response()->json(['message' => 'Quotation not found'], 404);
        }

        // Verificar tipo de cambio si currencyType = USD
        $exchangeRate = Currency::where('date', $request->date ?? $quotation->date)->first();
        if (($request->input('currencyType') === 'USD') && !$exchangeRate) {
            return response()->json(['error' => 'No se ha registrado el tipo de cambio para la fecha seleccionada'], 422);
        }

        // igvActive
        $igvActive = $request->has('igvActive')
            ? (bool)$request->igvActive
            : $quotation->igvActive;

        // status
        $status = $request->filled('status')
            ? $request->input('status')
            : $quotation->status;

        // Actualizar datos principales
        $quotation->update([
            'detail'       => $request->input('detail', $quotation->detail),
            'date'         => $request->input('date', $quotation->date),
            'currencyType' => $request->input('currencyType', $quotation->currencyType),
            'discount'     => $request->input('discount', $quotation->discount),
            'client_id'    => $request->input('client_id', $quotation->client_id),
            'igvActive'    => $igvActive,
            'status'       => $status,
        ]);

        // Recalcular totales
        $totalMachinery  = 0;
        $totalSpareParts = 0;

        // detailMachinery
        if ($request->filled('detailMachinery')) {
            // Borramos detalle anterior
            $quotation->detailMachinery()->delete();

            // Agregamos la nueva lista de detalles
            $totalMachinery = $this->addDetailMachinery($request->detailMachinery, $quotation);
        }

        // detailSpareParts
        if ($request->filled('detailSpareParts')) {
            $quotation->detailSpareParts()->delete();
            $totalSpareParts = $this->addDetailSpareParts($request->detailSpareParts, $quotation);
        }

        // Calcular totales finales
        $quotation->totalMachinery  = $totalMachinery;
        $quotation->totalSpareParts = $totalSpareParts;
        $quotation->discount        = (float)$request->input('discount', $quotation->discount);
        $quotation->subtotal        = $totalMachinery + $totalSpareParts - $quotation->discount;
        $quotation->igv             = $igvActive ? round($quotation->subtotal * 0.18, 2) : 0;
        $quotation->total           = $quotation->subtotal + $quotation->igv;
        $quotation->save();

        $quotation->refresh();
        return response()->json(new QuotationResource($quotation));
    }

    /**
     * @OA\Delete(
     *     path="/taeyoung-backend/public/api/quotation/{id}",
     *     tags={"Quotation"},
     *     summary="Delete a quotation",
     *     description="Delete a quotation",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         parameter="id",
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Quotation ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Quotation deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Quotation not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Quotation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Quotation cannot be deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Quotation cannot be deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/Unauthenticated")
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) {
            return response()->json(['message' => 'Quotation not found'], 404);
        }

        if ($quotation->orders()->count() > 0) {
            return response()->json(['message' => 'Quotation has orders, cannot be deleted'], 409);
        }

        $quotation->detailMachinery()->delete();
        $quotation->detailSpareParts()->delete();
        $quotation->delete();

        return response()->json(['message' => 'Quotation deleted']);
    }

    /**
     * Crea los detalles de maquinaria, opcionalmente agrupando si deseas.
     * En este ejemplo, no se agrupan (cada item es un registro).
     */
    private function addDetailMachinery(array $detailMachinery, Quotation $quotation): float
    {
        $totalMachinery = 0;

        // Si quisieras agrupar por machinery_id, hazlo similar a detailSpareParts.
        // Aquí creamos uno a uno directamente.
        foreach ($detailMachinery as $detail) {
            $dm = DetailMachinery::create([
                'description'    => $detail['description'],
                'quantity'       => $detail['quantity'],
                'movementType'   => 'quotation',
                'purchasePrice'  => $detail['purchasePrice'] ?? 0,
                'purchaseValue'  => ($detail['purchasePrice'] ?? 0) * $detail['quantity'],
                'salePrice'      => (float)($detail['salePrice'] ?? 0),
                'saleValue'      => (float)($detail['salePrice'] ?? 0) * $detail['quantity'],
                'realPrice'      => $detail['realPrice'] ?? null,
                'contablePrice'  => $detail['contablePrice'] ?? null,
                'machinery_id'   => $detail['machinery_id'] ?? null,
                'quotation_id'   => $quotation->id,
            ]);
            $totalMachinery += $dm->saleValue;
        }

        return $totalMachinery;
    }

    /**
     * Crea los detalles de repuestos (detailSpareParts), 
     * agrupando por spare_part_id si está repetido.
     */
    private function addDetailSpareParts(array $detailSpareParts, Quotation $quotation): float
    {
        $detailSparePartsValidate = [];
        $totalSpareParts = 0;

        // Agrupar items repetidos
        foreach ($detailSpareParts as $detail) {
            $sparePartId = $detail['spare_part_id'];
            if (isset($detailSparePartsValidate[$sparePartId])) {
                $detailSparePartsValidate[$sparePartId]['quantity'] += $detail['quantity'];
            } else {
                $detailSparePartsValidate[$sparePartId] = $detail;
            }
        }

        // Crear registros y sumar total
        foreach ($detailSparePartsValidate as $detail) {
            $dsp = DetailSparePart::create([
                'quantity'       => $detail['quantity'],
                'movementType'   => 'quotation',
                'purchasePrice'  => $detail['purchasePrice'] ?? 0,
                'purchaseValue'  => ($detail['purchasePrice'] ?? 0) * $detail['quantity'],
                'salePrice'      => (float)($detail['salePrice'] ?? 0),
                'saleValue'      => (float)($detail['salePrice'] ?? 0) * $detail['quantity'],
                'realPrice'      => $detail['realPrice'] ?? null,
                'contablePrice'  => $detail['contablePrice'] ?? null,
                'spare_part_id'  => $detail['spare_part_id'],
                'quotation_id'   => $quotation->id,
            ]);

            $totalSpareParts += $dsp->saleValue;
        }

        return $totalSpareParts;
    }
}
