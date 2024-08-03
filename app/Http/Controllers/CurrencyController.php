<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCurrencyRequest;
use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
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
        $currency = Currency::create($request->validated());
        return response()->json(new CurrencyResource($currency));
    }

    public function show(int $id)
    {
        $currency = Currency::find($id);
        if (!$currency) return response()->json(['message' => 'Currency not found'], 404);
        return response()->json(new CurrencyResource($currency));
    }

    public function update(UpdateCurrencyRequest $request, int $id)
    {
        $currency = Currency::find($id);
        if (!$currency) return response()->json(['message' => 'Currency not found'], 404);

        $data = $request->validated();
        $data = array_filter($data, fn($value) => $value !== null);

        $currency->update($data);

        return response()->json(new CurrencyResource($currency));
    }

    public function destroy(int $id)
    {
        $currency = Currency::find($id);
        if (!$currency) return response()->json(['message' => 'Currency not found'], 404);
        $currency->delete();
        return response()->json(['message' => 'Currency deleted']);
    }
}
