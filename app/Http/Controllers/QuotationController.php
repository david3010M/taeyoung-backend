<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexQuotationRequest;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Http\Resources\CountryResource;
use App\Models\Quotation;

class QuotationController extends Controller
{
    public function index(IndexQuotationRequest $request)
    {
        return $this->getFilteredResults(
            Quotation::class,
            $request,
            Quotation::filters,
            CountryResource::class
        );
    }

    public function store(StoreQuotationRequest $request)
    {
        //
    }

    public function show(int $id)
    {
        //
    }

    public function update(UpdateQuotationRequest $request, int $id)
    {
        //
    }

    public function destroy(int $id)
    {
        //
    }
}
