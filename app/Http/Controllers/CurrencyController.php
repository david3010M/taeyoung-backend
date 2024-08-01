<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCurrencyRequest;
use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\UserResource;
use App\Models\Currency;
use App\Models\User;
use App\Traits\Filterable;

class CurrencyController extends Controller
{
    use Filterable;

    public function index(IndexCurrencyRequest $request)
    {
        return $this->getFilteredResults(
            Currency::class,
            $request,
            Currency::filters,
            CurrencyResource::class
        );
    }

    public function store(StoreCurrencyRequest $request)
    {
        //
    }

    public function show(Currency $currency)
    {
        //
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {
        //
    }

    public function destroy(Currency $currency)
    {
        //
    }
}
