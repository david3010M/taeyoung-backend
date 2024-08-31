<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexQuotationRequest;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\QuotationResource;
use App\Models\DetailMachinery;
use App\Models\DetailSparePart;
use App\Models\Quotation;
use App\Models\SparePart;
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
     *     @OA\Parameter(parameter="all", name="all", in="query", required=false, description="Get all units", @OA\Schema(type="boolean")),
     *     @OA\Parameter(parameter="page", name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="per_page", name="per_page", in="query", required=false, description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter(parameter="number", name="number", in="query", required=false, description="Filter by number", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="date", name="date[]", in="query", required=false, description="Filter by date", @OA\Schema(type="array", @OA\Items(type="string"))),
     *     @OA\Parameter(parameter="detail", name="detail", in="query", required=false, description="Filter by detail", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="client$filterName", name="client$filterName", in="query", required=false, description="Filter by client name", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/PurchaseCollection")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexQuotationRequest $request)
    {
        return $this->getFilteredResults(
            Quotation::class,
            $request,
            Quotation::filters,
            Quotation::sorts,
            QuotationResource::class
        );
    }

    /**
     * @OA\Post (
     *     path="/taeyoung-backend/public/api/quotation",
     *     tags={"Quotation"},
     *     summary="Store a new quotation",
     *     description="Store a new quotation",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(required=true, description="Quotation data", @OA\JsonContent(ref="#/components/schemas/StoreQuotationRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/QuotationResource")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     *
     */
    public function store(StoreQuotationRequest $request)
    {
        $dataQuotation = [
            'number' => $this->nextCorrelative(Quotation::class, 'number'),
            'date' => $request->input('date'),
            'detail' => $request->input('detail'),
            'currencyType' => $request->input('currencyType'),
            'discount' => $request->input('discount'),
            'client_id' => $request->input('client_id'),
        ];

        $quotation = Quotation::create($dataQuotation);
        $totalMachinery = 0;
        $totalSpareParts = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpareParts = $request->input('detailSpareParts');

        if ($detailMachinery) {
            foreach ($detailMachinery as $detail) {
                $detailMachinery = DetailMachinery::create([
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'movementType' => 'quotation',
                    'salePrice' => $detail['salePrice'],
                    'saleValue' => $detail['salePrice'] * $detail['quantity'],
                    'quotation_id' => $quotation->id,
                ]);
                $totalMachinery += $detailMachinery->salePrice * $detailMachinery->quantity;
            }
        }

        if ($detailSpareParts) {
            $totalSpareParts = $this->addDetailSpareParts($detailSpareParts, $quotation);
        }

        $quotation->totalSpareParts = $totalSpareParts;
        $quotation->totalMachinery = $totalMachinery;
        $quotation->subtotal = $totalMachinery + $totalSpareParts;
        $quotation->igv = $quotation->subtotal * 0.18;
        $quotation->discount = (float)$request->input('discount');
        $quotation->total = $quotation->subtotal + $quotation->igv - $quotation->discount;
        $quotation->update();

        $quotation = Quotation::find($quotation->id);
        return response()->json(new QuotationResource($quotation));
    }

    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/quotation/{id}",
     *     tags={"Quotation"},
     *     summary="Show a quotation",
     *     description="Show a quotation",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Quotation ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/QuotationResource")),
     *     @OA\Response(response=404, description="Quotation not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Quotation not found"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     *
     */
    public function show(int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) return response()->json(['message' => 'Quotation not found'], 404);
        return response()->json(new QuotationResource($quotation));
    }

    /**
     * @OA\Put (
     *     path="/taeyoung-backend/public/api/quotation/{id}",
     *     tags={"Quotation"},
     *     summary="Update a quotation",
     *     description="Update a quotation",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Quotation ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, description="Quotation data", @OA\JsonContent(ref="#/components/schemas/UpdateQuotationRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/QuotationResource")),
     *     @OA\Response(response=404, description="Quotation not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Quotation not found"))),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function update(UpdateQuotationRequest $request, int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) return response()->json(['message' => 'Quotation not found'], 404);

        $data = [
            'detail' => $request->input('detail') ?? $quotation->detail,
            'date' => $request->input('date') ?? $quotation->date,
            'currencyType' => $request->input('currencyType') ?? $quotation->currencyType,
            'discount' => $request->input('discount') ?? $quotation->discount,
            'client_id' => $request->input('client_id') ?? $quotation->client_id,
        ];
        $quotation->update($data);
        $totalMachinery = 0;
        $totalSpareParts = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpareParts = $request->input('detailSpareParts');

        if ($detailMachinery) {
            $quotation->detailMachinery()->delete();
            foreach ($detailMachinery as $detail) {
                $detailMachinery = DetailMachinery::create([
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'movementType' => 'quotation',
                    'salePrice' => $detail['salePrice'],
                    'saleValue' => $detail['salePrice'] * $detail['quantity'],
                    'quotation_id' => $quotation->id,
                ]);
                $totalMachinery += $detailMachinery->saleValue;
            }
        }

        if ($detailSpareParts) {
            $quotation->detailSpareParts()->delete();
            $totalSpareParts = $this->addDetailSpareParts($detailSpareParts, $quotation);
        }

        $quotation->totalSpareParts = $totalSpareParts;
        $quotation->totalMachinery = $totalMachinery;
        $quotation->subtotal = $totalMachinery + $totalSpareParts;
        $quotation->igv = $quotation->subtotal * 0.18;
        $quotation->discount = (float)$request->input('discount');
        $quotation->total = $quotation->subtotal + $quotation->igv - $quotation->discount;
        $quotation->update();


        $quotation = Quotation::find($quotation->id);
        return response()->json(new QuotationResource($quotation));
    }

    /**
     * @OA\Delete (
     *     path="/taeyoung-backend/public/api/quotation/{id}",
     *     tags={"Quotation"},
     *     summary="Delete a quotation",
     *     description="Delete a quotation",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Quotation ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Quotation deleted"))),
     *     @OA\Response(response=404, description="Quotation not found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Quotation not found"))),
     *     @OA\Response(response=409, description="Quotation cannot be deleted", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Quotation cannot be deleted"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function destroy(int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) return response()->json(['message' => 'Quotation not found'], 404);
        if ($quotation->orders()->count() > 0) {
            return response()->json(['message' => 'Quotation has orders, cannot be deleted'], 409);
        }
        $quotation->detailMachinery()->delete();
        $quotation->detailSpareParts()->delete();
        $quotation->delete();
        return response()->json(['message' => 'Quotation deleted']);
    }

    private function addDetailSpareParts(mixed $detailSpareParts, $quotation)
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
                'quotation_id' => $quotation->id,
            ]);
            $totalSpareParts += $detailSparePart->saleValue;
        }
        return $totalSpareParts;
    }
}
