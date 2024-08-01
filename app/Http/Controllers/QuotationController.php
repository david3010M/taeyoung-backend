<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexQuotationRequest;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use App\Traits\Filterable;

class QuotationController extends Controller
{
    use Filterable;

    public function index(IndexQuotationRequest $request)
    {
        return $this->getFilteredResults(
            Quotation::class,
            $request,
            Quotation::filters,
            QuotationResource::class
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
