<?php

namespace App\Http\Controllers;

use App\Http\Requests\country\CountryIndexRequest;
use App\Http\Requests\country\IndexCountryRequest;
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

    public function store(Request $request)
    {
        
    }

    public function show(int $id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }
        return new CountryResource($country);
    }

    public function update(Request $request, int $id)
    {

    }

    public function destroy(int $id)
    {
        //
    }
}
