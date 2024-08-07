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

    public function store(StoreQuotationRequest $request)
    {
        $dataQuotation = [
            'number' => $this->nextCorrelative(Quotation::class, 'number'),
            'detail' => $request->input('detail'),
            'date' => $request->input('date'),
            'currencyType' => $request->input('currencyType'),
            'initialPayment' => $request->input('initialPayment'),
            'debts' => $request->input('debts'),
            'client_id' => $request->input('client_id'),
            'currency_id' => $request->input('currency_id'),
        ];

        $quotation = Quotation::create($dataQuotation);
        $totalPrice = 0;

        $detailMachinery = $request->input('detailMachinery');
        $detailSpareParts = $request->input('detailSpareParts');

        if ($detailMachinery) {
            foreach ($detailMachinery as $detail) {
                $detailMachinery = DetailMachinery::create([
                    'description' => $detail['description'],
                    'quantity' => $detail['quantity'],
                    'movementType' => 'quotation',
                    'salePrice' => $detail['salePrice'],
                    'quotation_id' => $quotation->id,
                ]);
                $totalPrice += $detailMachinery->salePrice * $detailMachinery->quantity;
            }
        }

        if ($detailSpareParts) {
            $this->addDetailSpareParts($detailSpareParts, $quotation, $totalPrice);
        }

        $quotation->balance = $quotation->price - $quotation->initialPayment;
        $quotation->update();

        $quotation = Quotation::find($quotation->id);
        return response()->json(new QuotationResource($quotation));
    }

    public function show(int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) return response()->json(['message' => 'Quotation not found'], 404);
        return response()->json(new QuotationResource($quotation));
    }

    public function update(UpdateQuotationRequest $request, int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) return response()->json(['message' => 'Quotation not found'], 404);

        $data = [
            'detail' => $request->input('detail') ?? $quotation->detail,
            'date' => $request->input('date') ?? $quotation->date,
            'currencyType' => $request->input('currencyType') ?? $quotation->currencyType,
            'price' => $request->input('price') ?? $quotation->price,
            'initialPayment' => $request->input('initialPayment') ?? $quotation->initialPayment,
            'debts' => $request->input('debts') ?? $quotation->debts,
            'client_id' => $request->input('client_id') ?? $quotation->client_id,
            'currency_id' => $request->input('currency_id') ?? $quotation->currency_id
        ];
        $quotation->update($data);
        $totalPrice = 0;

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
                    'quotation_id' => $quotation->id,
                ]);
                $totalPrice += $detailMachinery->salePrice * $detailMachinery->quantity;
            }
        }

        if ($detailSpareParts) {
            $quotation->detailSpareParts()->delete();
            $this->addDetailSpareParts($detailSpareParts, $quotation, $totalPrice);
        }

        $quotation->balance = $quotation->price - $quotation->initialPayment;
        $quotation->update();

        $quotation = Quotation::find($quotation->id);
        return response()->json(new QuotationResource($quotation));
    }

    public function destroy(int $id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) return response()->json(['message' => 'Quotation not found'], 404);
        $quotation->delete();
        return response()->json(['message' => 'Quotation deleted']);
    }

    private function addDetailSpareParts(mixed $detailSpareParts, $quotation, $totalPrice): void
    {
        $detailSparePartsValidate = [];
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
                'spare_part_id' => $detail['spare_part_id'],
                'quotation_id' => $quotation->id,
            ]);
            $totalPrice += $detailSparePart->salePrice * $detailSparePart->quantity;
        }
    }
}
