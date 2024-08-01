<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCountryRequest;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Traits\Filterable;

class CountryController extends Controller
{
    use Filterable;

    public function index(IndexCountryRequest $request)
    {
        return $this->getFilteredResults(
            Country::class,
            $request,
            Country::filters,
            CountryResource::class
        );
    }

    public function store(StoreCountryRequest $request)
    {
        $country = Country::create($request->validated());
        return response()->json(new CountryResource($country));
    }

    public function show(int $id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }
        return response()->json(new CountryResource($country));
    }

    public function update(UpdateCountryRequest $request, int $id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }
        $country->update($request->validated());
        return response()->json(new CountryResource($country));
    }

    public function destroy(int $id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }
        $country->delete();
        return response()->json(['message' => 'Country deleted']);
    }
}
