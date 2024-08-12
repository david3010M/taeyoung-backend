<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexPaymentConceptRequest;
use App\Http\Resources\PaymentConceptResource;
use App\Models\PaymentConcept;
use App\Http\Requests\StorePaymentConceptRequest;
use App\Http\Requests\UpdatePaymentConceptRequest;

class PaymentConceptController extends Controller
{
    public function index(IndexPaymentConceptRequest $request)
    {
        return $this->getFilteredResults(
            PaymentConcept::class,
            $request,
            PaymentConcept::filters,
            PaymentConcept::sorts,
            PaymentConceptResource::class
        );
    }

    public function store(StorePaymentConceptRequest $request)
    {
        $number = $this->nextCorrelative(PaymentConcept::class, 'number');
        $paymentConcept = PaymentConcept::create(array_merge($request->validated(), ['number' => $number]));
        $paymentConcept = PaymentConcept::find($paymentConcept->id);
        return new PaymentConceptResource($paymentConcept);
    }

    public function show(int $id)
    {
        //
    }

    public function update(UpdatePaymentConceptRequest $request, int $id)
    {
        //
    }

    public function destroy(int $id)
    {
        //
    }
}
