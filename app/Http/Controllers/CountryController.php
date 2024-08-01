<?php

namespace App\Http\Controllers;

use App\Http\Requests\Country\CountryIndexRequest;
use App\Http\Requests\Country\CreateCountryRequest;
use App\Http\Requests\Country\IndexCountryRequest;
use App\Http\Requests\Country\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\User;
use App\Traits\Filterable;
use Illuminate\Http\Request;

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

    public function store(CreateCountryRequest $request)
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
