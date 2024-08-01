<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quotation\CreateQuotationRequest;
use App\Http\Requests\Quotation\IndexQuotationRequest;
use App\Http\Requests\Quotation\UpdateQuotationRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Models\Quotation;
use Illuminate\Http\Request;

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

    public function store(CreateQuotationRequest $request)
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
