<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/country",
     *     tags={"Countries"},
     *     security={{"bearerAuth": {}}},
     *     summary="List of countries",
     *     @OA\Response(
     *         response=200,
     *         description="List of countries",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Country")
     *         )
     *    )
     * )
     *
     */
    public function index()
    {
        return response()->json(Country::all());
    }

    public function store(Request $request)
    {
        //
    }

    public function show(int $id)
    {
        //
    }

    public function update(Request $request, int $id)
    {
        //
    }

    public function destroy(int $id)
    {
        //
    }
}
